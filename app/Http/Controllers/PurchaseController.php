<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Payment;
use App\Models\BankAccount;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view purchases');
        $query = Purchase::with(['supplier', 'creator'])->latest();

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('po_number', 'like', "%{$request->search}%")
                  ->orWhere('supplier_name', 'like', "%{$request->search}%")
                  ->orWhere('supplier_phone', 'like', "%{$request->search}%")
                  ->orWhereHas('supplier', function($sq) use ($request) {
                      $sq->where('name', 'like', "%{$request->search}%")
                         ->orWhere('company_name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('supplier_id') && $request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $purchases = $query->paginate(15)->appends($request->query());
        $suppliers = Supplier::active()->latest()->get();

        $stats = [
            'total'      => Purchase::count(),
            'received'   => Purchase::where('status', 'received')->count(),
            'pending'    => Purchase::whereIn('status', ['pending','ordered','draft'])->count(),
            'total_due'  => Purchase::sum('due_amount'),
            'total_cost' => Purchase::where('status', 'received')->sum('total_amount'),
        ];

        return view('purchases.purchases.index', compact('purchases', 'suppliers', 'stats'));
    }

    public function create()
    {
        $this->authorizePermission('create purchases');
        $suppliers = Supplier::active()->latest()->get();
        $products = Product::active()->orderBy('name')->get();
        $bankAccounts = BankAccount::active()->orderBy('account_name')->get();
        return view('purchases.purchases.create', compact('suppliers', 'products', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create purchases');
        $requireSupplier = function_exists('settings') && settings('purchases.require_supplier');
        $supplierRules = [
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'supplier_phone' => ['nullable', 'string', 'max:50'],
        ];
        if ($requireSupplier) {
            $supplierRules['supplier_name'][] = 'required_without:supplier_id';
            $supplierRules['supplier_phone'][] = 'required_without:supplier_id';
        }
        $validated = $request->validate(array_merge($supplierRules, [
            'supplier_address' => ['nullable', 'string'],
            'order_date' => ['required', 'date'],
            'expected_delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id', 'required_if:payment_method,card,mobile_banking,bank_transfer,cheque'],
            'notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.barcode' => ['required', 'string'], // Will be made unique in controller
            'items.*.serial_number' => ['nullable', 'string'],
            'items.*.cost_price' => ['required', 'numeric', 'min:0'],
            'items.*.selling_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.condition_notes' => ['nullable', 'string'],
            'items.*.warranty_info' => ['nullable', 'string'],
            'items.*.notes' => ['nullable', 'string'],
        ]));

        // Use database transaction for data consistency
        $purchase = \DB::transaction(function () use ($validated) {
            // Create purchase order
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'] ?? null,
                'supplier_name' => $validated['supplier_name'] ?? null,
                'supplier_phone' => $validated['supplier_phone'] ?? null,
                'supplier_address' => $validated['supplier_address'] ?? null,
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'paid_amount' => $validated['paid_amount'] ?? 0,
                'payment_method' => $validated['payment_method'] ?? 'cash',
                'bank_account_id' => $validated['bank_account_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'internal_notes' => $validated['internal_notes'] ?? null,
                'status' => 'pending',
            ]);

            // Pre-load products to avoid N+1 queries
            $productIds = array_unique(array_column($validated['items'], 'product_id'));
            $products = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');

            // Create purchase items - each barcode = 1 stock unit
            // JavaScript sends items with unique barcodes already (with suffixes if quantity > 1)
            // Each item in the array represents one stock unit with its barcode
            foreach ($validated['items'] as $itemData) {
                $barcode = $itemData['barcode'];
                $product = $products->get($itemData['product_id']);
                
                // Check if barcode already exists in this product's barcodes
                if ($product && $product->hasBarcode($barcode)) {
                    // Barcode already exists for this product, skip or warn
                    continue; // Skip duplicate barcode for same product
                }
                
                PurchaseItem::create([
                    'purchase_order_id' => $purchase->id,
                    'product_id' => $itemData['product_id'],
                    'barcode' => $barcode,
                    'serial_number' => $itemData['serial_number'] ?? null,
                    'cost_price' => $itemData['cost_price'],
                    'selling_price' => $itemData['selling_price'] ?? null,
                    'quantity' => 1, // Each barcode = 1 stock unit
                    'status' => 'received', // Auto-mark as received
                    'received_date' => now(), // Set received date
                    'condition_notes' => $itemData['condition_notes'] ?? null,
                    'warranty_info' => $itemData['warranty_info'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                ]);
                
                // Stock is automatically updated via PurchaseItem boot() event listener
            }

            // Mark purchase order as received
            $purchase->status = 'received';
            $purchase->received_date = now();
            if (auth()->check()) {
                $purchase->received_by = auth()->id();
            }

            // Calculate totals
            $purchase->calculateTotals();
            
            // Create journal entry for purchase (automatically received)
            AccountingService::recordPurchase($purchase);
            
            return $purchase;
        });

        \App\Services\SmsNotificationService::purchaseReceived($purchase);

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase order created and received successfully. Stock updated.');
    }

    public function show(Purchase $purchase)
    {
        $this->authorizePermission('view purchases');
        $purchase->load(Purchase::getStandardRelations());
        return view('purchases.purchases.show', compact('purchase'));
    }

    public function printInvoice(Purchase $purchase)
    {
        $this->authorizePermission('print purchase-invoices');
        $purchase->load(['supplier', 'items.product', 'creator', 'receiver']);
        return view('purchases.purchases.print', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $this->authorizePermission('edit purchases');
        $suppliers = Supplier::active()->latest()->get();
        $products = Product::active()->orderBy('name')->get();
        $bankAccounts = BankAccount::active()->orderBy('account_name')->get();
        $purchase->load('items.product');
        
        return view('purchases.purchases.edit', compact('purchase', 'suppliers', 'products', 'bankAccounts'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $this->authorizePermission('edit purchases');
        $requireSupplier = function_exists('settings') && settings('purchases.require_supplier');
        $supplierRules = [
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'supplier_phone' => ['nullable', 'string', 'max:50'],
        ];
        if ($requireSupplier) {
            $supplierRules['supplier_name'][] = 'required_without:supplier_id';
            $supplierRules['supplier_phone'][] = 'required_without:supplier_id';
        }
        $validated = $request->validate(array_merge($supplierRules, [
            'supplier_address' => ['nullable', 'string'],
            'order_date' => ['required', 'date'],
            'expected_delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id', 'required_if:payment_method,card,mobile_banking,bank_transfer,cheque'],
            'notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ]));

        $purchase->update([
            'supplier_id' => $validated['supplier_id'] ?? null,
            'supplier_name' => $validated['supplier_name'] ?? null,
            'supplier_phone' => $validated['supplier_phone'] ?? null,
            'supplier_address' => $validated['supplier_address'] ?? null,
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
            'tax_amount' => $validated['tax_amount'] ?? 0,
            'discount_amount' => $validated['discount_amount'] ?? 0,
            'paid_amount' => $validated['paid_amount'] ?? 0,
            'payment_method' => $validated['payment_method'],
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'internal_notes' => $validated['internal_notes'] ?? null,
        ]);
        $purchase->calculateTotals();

        // Update journal entry if needed
        AccountingService::recordPurchase($purchase);

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase order updated successfully.');
    }

    /**
     * Record an additional payment against a purchase order.
     */
    public function collectPayment(Request $request, Purchase $purchase)
    {
        $this->authorizePermission('edit purchases');

        if ($purchase->due_amount <= 0) {
            return back()->with('error', 'This purchase order has no outstanding due amount.');
        }

        $validated = $request->validate([
            'payment_amount'  => ['required', 'numeric', 'min:0.01', 'max:' . $purchase->due_amount],
            'payment_method'  => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id', 'required_if:payment_method,card,mobile_banking,bank_transfer,cheque'],
        ], [
            'payment_amount.max' => 'Payment cannot exceed the due amount (৳' . number_format($purchase->due_amount, 2) . ').',
        ]);

        DB::transaction(function () use ($purchase, $validated) {
            $payment = Payment::create([
                'payment_type'     => 'supplier',
                'purchase_id'      => $purchase->id,
                'supplier_id'      => $purchase->supplier_id,
                'amount'           => $validated['payment_amount'],
                'payment_date'     => now()->toDateString(),
                'payment_method'   => $validated['payment_method'],
                'bank_account_id'  => $validated['bank_account_id'] ?? $purchase->bank_account_id,
                'reference_number' => null,
                'notes'            => 'Payment for PO ' . $purchase->po_number,
            ]);
            AccountingService::recordPayment($payment);
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Payment of ৳' . number_format($validated['payment_amount'], 2) . ' recorded successfully.');
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->status === 'received') {
            return redirect()->route('purchases.index')
                ->with('error', 'Cannot delete a received purchase order.');
        }

        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase order deleted successfully.');
    }

    public function receive(Purchase $purchase)
    {
        $this->authorizePermission('receive purchases');
        $purchase->load(['items.product', 'supplier']);
        return view('purchases.purchases.receive', compact('purchase'));
    }

    public function receiveItem(Request $request, Purchase $purchase, PurchaseItem $item)
    {
        $this->authorizePermission('receive purchases');
        if ($item->purchase_order_id != $purchase->id) {
            return back()->with('error', 'Invalid item for this purchase order.');
        }

        if ($item->status === 'received') {
            return back()->with('error', 'Item already received.');
        }

        $item->markAsReceived();

        return back()->with('success', 'Item received successfully. Stock updated.');
    }

    public function receiveMultipleItems(Request $request, Purchase $purchase)
    {
        $this->authorizePermission('receive purchases');
        $validated = $request->validate([
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['exists:purchase_items,id'],
        ]);

        $items = PurchaseItem::whereIn('id', $validated['item_ids'])
            ->where('purchase_order_id', $purchase->id)
            ->where('status', '!=', 'received')
            ->get();

        foreach ($items as $item) {
            $item->markAsReceived();
        }

        return back()->with('success', count($items) . ' items received successfully.');
    }
}
