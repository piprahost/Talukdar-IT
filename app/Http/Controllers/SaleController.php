<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\BankAccount;
use App\Models\StockMovement;
use App\Models\Warranty;
use App\Models\WarrantySubmission;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view sales');
        $query = Sale::with(['customer', 'creator'])
            ->withCount(['returns as completed_returns_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->latest();

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

        $stats = [
            'total'         => Sale::count(),
            'completed'     => Sale::where('status', 'completed')->count(),
            'draft'         => Sale::where('status', 'draft')->count(),
            'total_revenue' => Sale::where('status', 'completed')->sum('total_amount'),
            'total_due'     => Sale::where('payment_status', '!=', 'paid')->sum('due_amount'),
            'unpaid_count'  => Sale::where('payment_status', 'unpaid')->count(),
        ];

        return view('sales.sales.index', compact('sales', 'customers', 'stats'));
    }

    public function create()
    {
        $this->authorizePermission('create sales');
        $customers = Customer::active()->latest()->get();
        $products = Product::active()->orderBy('name')->get();
        $bankAccounts = BankAccount::active()->orderBy('account_name')->get();
        $defaultPaymentMethod = function_exists('settings') ? (settings('payments.default_payment_method') ?: 'cash') : 'cash';
        $defaultPaymentTermsDays = function_exists('settings') ? (int) settings('sales.default_payment_terms_days', 0) : 0;
        $defaultDueDate = $defaultPaymentTermsDays > 0 ? now()->addDays($defaultPaymentTermsDays)->format('Y-m-d') : null;
        return view('sales.sales.create', compact('customers', 'products', 'bankAccounts', 'defaultPaymentMethod', 'defaultPaymentTermsDays', 'defaultDueDate'));
    }

    public function quickSell()
    {
        $this->authorizePermission('create sales');
        $customers = Customer::active()->latest()->get();
        $products = Product::active()->orderBy('name')->get();
        $bankAccounts = BankAccount::active()->orderBy('account_name')->get();
        $defaultPaymentMethod = function_exists('settings') ? (settings('payments.default_payment_method') ?: 'cash') : 'cash';
        return view('sales.quick-sell', compact('customers', 'products', 'bankAccounts', 'defaultPaymentMethod'));
    }

    public function quickSellStore(Request $request)
    {
        $this->authorizePermission('create sales');
        $validated = $request->validate([
            'customer_id'      => ['nullable', 'exists:customers,id'],
            'customer_name'    => ['nullable', 'string', 'max:255'],
            'customer_phone'   => ['nullable', 'string', 'max:20'],
            'customer_address' => ['nullable', 'string'],
            'payment_method'   => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            'bank_account_id'  => ['nullable', 'exists:bank_accounts,id', 'required_if:payment_method,card,mobile_banking,bank_transfer,cheque'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.barcode'    => ['nullable', 'string'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount'   => ['nullable', 'numeric', 'min:0'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.notes'      => ['nullable', 'string'],
        ]);

        $sale = DB::transaction(function () use ($validated) {
            $sale = Sale::create([
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'sale_date' => now()->toDateString(),
                'due_date' => null,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'paid_amount' => 0,
                'payment_method' => $validated['payment_method'],
                'bank_account_id' => $validated['bank_account_id'] ?? null,
                'notes' => null,
                'internal_notes' => 'Quick sell',
                'status' => 'completed',
            ]);

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

            $sale->calculateTotals();
            AccountingService::recordSale($sale);
            return $sale;
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Quick sell completed. Invoice ' . $sale->invoice_number);
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create sales');
        $customerOptional = function_exists('settings') && settings('sales.customer_optional');
        $validated = $request->validate([
            'customer_id'      => [
                Rule::requiredIf(!$customerOptional && empty($request->customer_name)),
                'nullable',
                'exists:customers,id',
            ],
            'customer_name'    => [
                Rule::requiredIf(!$customerOptional && empty($request->customer_id)),
                'nullable',
                'string',
                'max:255',
            ],
            'customer_phone'   => ['nullable', 'string', 'max:20'],
            'customer_address' => ['nullable', 'string'],
            'sale_date'        => ['required', 'date'],
            'due_date'         => ['nullable', 'date', 'after_or_equal:sale_date'],
            'tax_amount'       => ['nullable', 'numeric', 'min:0'],
            'discount_amount'  => ['nullable', 'numeric', 'min:0'],
            'paid_amount'      => ['nullable', 'numeric', 'min:0'],
            'payment_method'   => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            'bank_account_id'  => ['nullable', 'exists:bank_accounts,id', 'required_if:payment_method,card,mobile_banking,bank_transfer,cheque'],
            'notes'            => ['nullable', 'string'],
            'internal_notes'   => ['nullable', 'string'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.barcode'    => ['nullable', 'string'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount'   => ['nullable', 'numeric', 'min:0'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.notes'      => ['nullable', 'string'],
            'status'             => ['nullable', 'in:draft,completed'],
        ]);

        $defaultTermsDays = function_exists('settings') ? (int) settings('sales.default_payment_terms_days', 0) : 0;
        $dueDate = $validated['due_date'] ?? null;
        if ($dueDate === null && $defaultTermsDays > 0) {
            $dueDate = \Carbon\Carbon::parse($validated['sale_date'])->addDays($defaultTermsDays)->toDateString();
        }
        // Use database transaction for data consistency
        $sale = \DB::transaction(function () use ($validated, $dueDate) {
            // Create sale
            $sale = Sale::create([
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'sale_date' => $validated['sale_date'],
                'due_date' => $dueDate,
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
        $sale->load(Sale::getStandardRelations());
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

        $customerOptional = function_exists('settings') && settings('sales.customer_optional');
        $validated = $request->validate([
            'customer_id' => [
                Rule::requiredIf(!$customerOptional && empty($request->customer_name)),
                'nullable',
                'exists:customers,id',
            ],
            'customer_name' => [
                Rule::requiredIf(!$customerOptional && empty($request->customer_id)),
                'nullable',
                'string',
                'max:255',
            ],
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

        DB::transaction(function () use ($sale) {
            $sale->load(['items.product', 'payments', 'returns.items.product']);

            $returnIds = $sale->returns->pluck('id')->filter()->values()->all();

            if ($sale->status === 'completed') {
                $productIds = $sale->items->pluck('product_id')
                    ->merge($sale->returns->flatMap->items->pluck('product_id'))
                    ->filter()
                    ->unique()
                    ->values();
                Product::whereIn('id', $productIds)->lockForUpdate()->get();

                foreach ($sale->returns as $return) {
                    AccountingService::deleteJournalEntry('sale_return', $return->id);

                    if ($return->status === 'completed') {
                        Payment::where('sale_id', $sale->id)
                            ->where('amount', '<', 0)
                            ->where('notes', 'Refund – Sale Return ' . $return->return_number)
                            ->forceDelete();

                        foreach ($return->items as $ri) {
                            $ri->loadMissing('product');
                            if (!$ri->product) {
                                continue;
                            }
                            $notes = "Sale deleted: reverse return {$return->return_number}";
                            if ($ri->barcode) {
                                if ($ri->product->hasBarcode($ri->barcode)) {
                                    $ri->product->removeBarcode($ri->barcode);
                                }
                            } else {
                                $ri->product->reduceStock(
                                    (int) $ri->quantity,
                                    'out',
                                    $notes,
                                    'sale_return',
                                    $return->id
                                );
                            }
                        }
                    }

                    $return->items()->delete();
                    $return->forceDelete();
                }

                foreach ($sale->payments as $payment) {
                    AccountingService::deleteJournalEntry('payment', $payment->id);
                    $payment->forceDelete();
                }

                AccountingService::deleteJournalEntry('sale', $sale->id);

                foreach ($sale->items as $item) {
                    $item->loadMissing('product');
                    if (!$item->product) {
                        continue;
                    }
                    $note = "Sale deleted: {$sale->invoice_number}";
                    if ($item->barcode) {
                        $item->product->addBarcode($item->barcode, true, $note);
                    } else {
                        $item->product->addStock(
                            (int) $item->quantity,
                            'in',
                            $note,
                            'sale',
                            $sale->id
                        );
                    }
                }

                $warrantyIds = Warranty::where('sale_id', $sale->id)->pluck('id');
                if ($warrantyIds->isNotEmpty()) {
                    WarrantySubmission::whereIn('warranty_id', $warrantyIds)->forceDelete();
                    Warranty::whereIn('id', $warrantyIds)->delete();
                }

                StockMovement::where('reference_type', 'sale')
                    ->where('reference_id', $sale->id)
                    ->delete();
                if (!empty($returnIds)) {
                    StockMovement::where('reference_type', 'sale_return')
                        ->whereIn('reference_id', $returnIds)
                        ->delete();
                }
            } else {
                foreach ($sale->returns as $return) {
                    $return->items()->delete();
                    $return->forceDelete();
                }
                foreach ($sale->payments as $payment) {
                    AccountingService::deleteJournalEntry('payment', $payment->id);
                    $payment->forceDelete();
                }
            }

            $sale->items()->delete();
            $sale->forceDelete();
        });

        return redirect()->route('sales.index')
            ->with('success', 'Sale and related accounting records deleted successfully.');
    }

    public function complete(Sale $sale)
    {
        $this->authorizePermission('complete sales');
        if ($sale->status === 'completed') {
            return back()->with('error', 'Sale is already completed.');
        }

        $allowNegativeStock = function_exists('settings') && settings('sales.allow_negative_stock');
        if (!$allowNegativeStock) {
            foreach ($sale->items as $item) {
                $item->load('product');
                $product = $item->product;
                $available = $product->display_stock ?? $product->stock_quantity ?? 0;
                $needed = $item->barcode && $product->hasBarcode($item->barcode) ? 1 : (int) $item->quantity;
                if ($available < $needed) {
                    return back()->with('error', "Insufficient stock for \"{$product->name}\". Available: {$available}, needed: {$needed}. Enable \"Allow negative stock\" in Sales settings to override.");
                }
            }
        }

        $sale->status = 'completed';
        $sale->save();

        // Recalculate totals so amounts are in sync before accounting
        $sale->calculateTotals();
        $sale->refresh();

        // Update product stock and create warranties
        foreach ($sale->items as $item) {
            $item->updateProductStock();
            // Create warranty if doesn't exist
            if (!$item->warranty()->exists()) {
                $item->createWarranty();
            }
        }

        // Post sale to accounting (so completed sales always have a journal entry)
        AccountingService::recordSale($sale);

        // Optional SMS notification
        \App\Services\SmsNotificationService::saleCompleted($sale);

        // Low stock SMS alerts (one per product that is now at or below threshold)
        foreach ($sale->items as $item) {
            $item->load('product');
            if ($item->product) {
                \App\Services\SmsNotificationService::lowStockAlert($item->product->fresh());
            }
        }

        return back()->with('success', 'Sale completed successfully. Stock updated.');
    }

    public function collectPayment(Request $request, Sale $sale)
    {
        $this->authorizePermission('edit sales');

        if ($sale->due_amount <= 0) {
            return back()->with('error', 'This sale has no outstanding due amount.');
        }

        $validated = $request->validate([
            'payment_amount'  => ['required', 'numeric', 'min:0.01', 'max:' . $sale->due_amount],
            'payment_method'  => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id', 'required_if:payment_method,card,mobile_banking,bank_transfer,cheque'],
        ], [
            'payment_amount.max' => 'Payment amount cannot exceed the due amount (' . (function_exists('money') ? money($sale->due_amount, 2) : '৳' . number_format($sale->due_amount, 2)) . ').',
        ]);

        $payment = DB::transaction(function () use ($sale, $validated) {
            $payment = Payment::create([
                'payment_type'   => 'customer',
                'sale_id'        => $sale->id,
                'customer_id'    => $sale->customer_id,
                'amount'         => $validated['payment_amount'],
                'payment_date'   => now()->toDateString(),
                'payment_method' => $validated['payment_method'],
                'bank_account_id'=> $validated['bank_account_id'] ?? $sale->bank_account_id,
                'reference_number' => null,
                'notes'          => 'Collected from invoice ' . $sale->invoice_number,
            ]);
            // Payment observer updates sale paid_amount/due_amount; post to accounting
            AccountingService::recordPayment($payment);
            return $payment;
        });

        // Optional SMS notification for customer payment
        \App\Services\SmsNotificationService::customerPayment($payment);

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Payment of ' . (function_exists('money') ? money($validated['payment_amount'], 2) : '৳' . number_format($validated['payment_amount'], 2)) . ' collected successfully.');
    }

    public function printInvoice(Sale $sale)
    {
        $this->authorizePermission('print invoices');
        $sale->load(Sale::getStandardRelations());
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

        $purchaseItem = PurchaseItem::latestReceivedForBarcode((int) $product->id, (string) $barcode);
        $purchaseUnitCost = $purchaseItem ? (float) $purchaseItem->cost_price : null;
        $suggestedUnitPrice = $product->selling_price;
        if ($purchaseItem && $purchaseItem->selling_price !== null && (float) $purchaseItem->selling_price > 0) {
            $suggestedUnitPrice = (float) $purchaseItem->selling_price;
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'selling_price' => (float) $product->selling_price,
            'cost_price' => $product->cost_price !== null ? (float) $product->cost_price : null,
            'stock_quantity' => (int) $product->stock_quantity,
            'barcode' => $barcode,
            'purchase_unit_cost' => $purchaseUnitCost,
            'suggested_unit_price' => (float) $suggestedUnitPrice,
        ]);
    }
}
