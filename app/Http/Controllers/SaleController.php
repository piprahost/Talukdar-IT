<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\BankAccount;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view sales');
        $query = Sale::with(['customer', 'creator'])->latest();

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('invoice_number', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%")
                  ->orWhereHas('customer', function($cq) use ($request) {
                      $cq->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        $sales = $query->paginate(15)->appends($request->query());
        $customers = Customer::active()->latest()->get();

        return view('sales.sales.index', compact('sales', 'customers'));
    }

    public function create()
    {
        $this->authorizePermission('create sales');
        $customers = Customer::active()->latest()->get();
        $products = Product::active()->orderBy('name')->get();
        $bankAccounts = BankAccount::active()->orderBy('account_name')->get();
        return view('sales.sales.create', compact('customers', 'products', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create sales');
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'customer_address' => ['nullable', 'string'],
            'sale_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:sale_date'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.barcode' => ['nullable', 'string'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string'],
            'status' => ['nullable', 'in:draft,completed'],
        ]);

        // Use database transaction for data consistency
        $sale = \DB::transaction(function () use ($validated) {
            // Create sale
            $sale = Sale::create([
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'sale_date' => $validated['sale_date'],
                'due_date' => $validated['due_date'] ?? null,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'paid_amount' => $validated['paid_amount'] ?? 0,
                'payment_method' => $validated['payment_method'],
                'bank_account_id' => $validated['bank_account_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'internal_notes' => $validated['internal_notes'] ?? null,
                'status' => $validated['status'] ?? 'draft',
            ]);

            // Create sale items
            // Stock will be automatically updated via SaleItem boot() event listener 
            // when items are created and sale status is 'completed'
            foreach ($validated['items'] as $itemData) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $itemData['product_id'],
                    'barcode' => $itemData['barcode'] ?? null,
                    'unit_price' => $itemData['unit_price'],
                    'discount' => $itemData['discount'] ?? 0,
                    'quantity' => $itemData['quantity'],
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            // Calculate totals
            $sale->calculateTotals();
            
            // Create journal entry if sale is completed
            if ($sale->status === 'completed') {
                AccountingService::recordSale($sale);
            }
            
            return $sale;
        });
        
        // Note: Stock is automatically updated via SaleItem boot() event listener
        // No need to manually update stock here to avoid double deduction

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Sale/Invoice created successfully.');
    }

    public function show(Sale $sale)
    {
        $this->authorizePermission('view sales');
        $sale->load(['customer', 'items.product', 'creator', 'returns', 'bankAccount']);
        return view('sales.sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $this->authorizePermission('edit sales');
        if ($sale->status === 'completed') {
            return redirect()->route('sales.show', $sale)
                ->with('error', 'Cannot edit a completed sale.');
        }

        $customers = Customer::active()->latest()->get();
        $products = Product::active()->orderBy('name')->get();
        $bankAccounts = BankAccount::active()->orderBy('account_name')->get();
        $sale->load('items.product');
        
        return view('sales.sales.edit', compact('sale', 'customers', 'products', 'bankAccounts'));
    }

    public function update(Request $request, Sale $sale)
    {
        $this->authorizePermission('edit sales');
        if ($sale->status === 'completed') {
            return redirect()->route('sales.show', $sale)
                ->with('error', 'Cannot update a completed sale.');
        }

        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'customer_address' => ['nullable', 'string'],
            'sale_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:sale_date'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id', 'required_if:payment_method,card,mobile_banking,bank_transfer,cheque'],
            'notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $sale->update($validated);
        $sale->calculateTotals();

        // Update journal entry if sale status changed to completed
        if ($sale->status === 'completed') {
            AccountingService::recordSale($sale);
        }

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale)
    {
        $this->authorizePermission('delete sales');
        if ($sale->status === 'completed') {
            return redirect()->route('sales.index')
                ->with('error', 'Cannot delete a completed sale.');
        }

        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Sale deleted successfully.');
    }

    public function complete(Sale $sale)
    {
        $this->authorizePermission('complete sales');
        if ($sale->status === 'completed') {
            return back()->with('error', 'Sale is already completed.');
        }

        $sale->status = 'completed';
        $sale->save();

        // Update product stock and create warranties
        foreach ($sale->items as $item) {
            $item->updateProductStock();
            // Create warranty if doesn't exist
            if (!$item->warranty()->exists()) {
                $item->createWarranty();
            }
        }

        return back()->with('success', 'Sale completed successfully. Stock updated.');
    }

    public function printInvoice(Sale $sale)
    {
        $this->authorizePermission('print invoices');
        $sale->load(['customer', 'items.product', 'creator']);
        return view('sales.sales.print', compact('sale'));
    }

    public function getProductByBarcode(Request $request)
    {
        // Check permission - user needs to be able to create sales to search products
        if (!auth()->user()->can('create sales')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $barcode = $request->get('barcode');
        
        if (!$barcode) {
            return response()->json(['error' => 'Barcode is required'], 400);
        }

        // Find product by barcode
        $product = Product::whereJsonContains('barcodes', $barcode)->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found for this barcode'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'selling_price' => $product->selling_price,
            'cost_price' => $product->cost_price,
            'stock_quantity' => $product->stock_quantity,
            'barcode' => $barcode,
        ]);
    }
}
