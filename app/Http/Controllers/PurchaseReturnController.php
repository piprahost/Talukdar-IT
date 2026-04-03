<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\PurchaseReturn;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view purchase-returns');
        $query = PurchaseReturn::with(['purchase', 'supplier', 'creator'])->latest();

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('return_number', 'like', "%{$request->search}%")
                  ->orWhereHas('purchase', function($pq) use ($request) {
                      $pq->where('po_number', 'like', "%{$request->search}%");
                  })
                  ->orWhereHas('supplier', function($sq) use ($request) {
                      $sq->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $returns = $query->paginate(15)->appends($request->query());
        
        return view('returns.purchase-returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $this->authorizePermission('create purchase-returns');
        $purchaseId = $request->get('purchase_id');
        $purchase = null;
        
        if ($purchaseId) {
            $purchase = Purchase::with(['items.product', 'supplier'])->findOrFail($purchaseId);
        }
        
        // Keep dropdown payload bounded to avoid heavy page render / 503 on large datasets.
        $purchases = Purchase::where('status', 'received')
            ->select(['id', 'po_number', 'supplier_id', 'order_date'])
            ->with(['supplier:id,name'])
            ->latest()
            ->limit(300)
            ->get();
        
        return view('returns.purchase-returns.create', compact('purchase', 'purchases'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create purchase-returns');
        $validated = $request->validate([
            'purchase_id' => ['required', 'exists:purchase_orders,id'],
            'return_date' => ['required', 'date'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.purchase_item_id' => ['required', 'exists:purchase_items,id'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.barcode' => ['nullable', 'string'],
            'items.*.cost_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.reason' => ['nullable', 'string'],
        ]);

        $purchase = Purchase::findOrFail($validated['purchase_id']);

        $return = DB::transaction(function () use ($validated, $purchase) {
            $return = PurchaseReturn::create([
                'purchase_id' => $validated['purchase_id'],
                'supplier_id' => $purchase->supplier_id,
                'return_date' => $validated['return_date'],
                'reason' => $validated['reason'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'status' => 'pending',
            ]);

            foreach ($validated['items'] as $itemData) {
                $return->items()->create([
                    'purchase_item_id' => $itemData['purchase_item_id'],
                    'product_id' => $itemData['product_id'],
                    'barcode' => $itemData['barcode'] ?? null,
                    'cost_price' => $itemData['cost_price'],
                    'quantity' => $itemData['quantity'],
                    'reason' => $itemData['reason'] ?? null,
                ]);
            }

            $return->calculateTotals();
            
            return $return;
        });

        return redirect()->route('purchase-returns.show', $return)
            ->with('success', 'Purchase return created successfully.');
    }

    public function show(PurchaseReturn $purchaseReturn)
    {
        $this->authorizePermission('view purchase-returns');
        $purchaseReturn->load(['purchase.supplier', 'supplier', 'items.product', 'items.purchaseItem', 'creator', 'approver']);
        return view('returns.purchase-returns.show', compact('purchaseReturn'));
    }

    public function edit(PurchaseReturn $purchaseReturn)
    {
        $this->authorizePermission('edit purchase-returns');
        if ($purchaseReturn->status !== 'pending') {
            return redirect()->route('purchase-returns.show', $purchaseReturn)
                ->with('error', 'Only pending returns can be edited.');
        }

        $purchaseReturn->load(['purchase.items.product', 'items.product', 'items.purchaseItem']);
        return view('returns.purchase-returns.edit', compact('purchaseReturn'));
    }

    public function update(Request $request, PurchaseReturn $purchaseReturn)
    {
        $this->authorizePermission('edit purchase-returns');
        if ($purchaseReturn->status !== 'pending') {
            return redirect()->route('purchase-returns.show', $purchaseReturn)
                ->with('error', 'Only pending returns can be edited.');
        }

        $validated = $request->validate([
            'return_date' => ['required', 'date'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'exists:purchase_return_items,id'],
            'items.*.purchase_item_id' => ['required', 'exists:purchase_items,id'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.barcode' => ['nullable', 'string'],
            'items.*.cost_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.reason' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($purchaseReturn, $validated) {
            $purchaseReturn->update([
                'return_date' => $validated['return_date'],
                'reason' => $validated['reason'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
            ]);

            // Delete existing items
            $purchaseReturn->items()->delete();

            // Create new items
            foreach ($validated['items'] as $itemData) {
                $purchaseReturn->items()->create([
                    'purchase_item_id' => $itemData['purchase_item_id'],
                    'product_id' => $itemData['product_id'],
                    'barcode' => $itemData['barcode'] ?? null,
                    'cost_price' => $itemData['cost_price'],
                    'quantity' => $itemData['quantity'],
                    'reason' => $itemData['reason'] ?? null,
                ]);
            }

            $purchaseReturn->calculateTotals();
        });

        return redirect()->route('purchase-returns.show', $purchaseReturn)
            ->with('success', 'Purchase return updated successfully.');
    }

    public function destroy(PurchaseReturn $purchaseReturn)
    {
        $this->authorizePermission('delete purchase-returns');

        DB::transaction(function () use ($purchaseReturn) {
            $purchaseReturn->load(['items.product']);

            if ($purchaseReturn->status === 'completed') {
                $productIds = $purchaseReturn->items->pluck('product_id')->filter()->unique()->values();
                Product::whereIn('id', $productIds)->lockForUpdate()->get();

                AccountingService::deleteJournalEntry('purchase_return', $purchaseReturn->id);

                Payment::where('purchase_id', $purchaseReturn->purchase_id)
                    ->where('amount', '<', 0)
                    ->where('notes', 'Credit – Purchase Return ' . $purchaseReturn->return_number)
                    ->forceDelete();

                foreach ($purchaseReturn->items as $ri) {
                    $ri->loadMissing('product');
                    if (!$ri->product) {
                        continue;
                    }
                    $notes = "Purchase return deleted: {$purchaseReturn->return_number}";
                    if ($ri->barcode) {
                        $ri->product->addBarcode($ri->barcode, true, $notes);
                    } else {
                        $ri->product->addStock(
                            (int) $ri->quantity,
                            'in',
                            $notes,
                            'purchase_return',
                            $purchaseReturn->id
                        );
                    }
                }

                StockMovement::where('reference_type', 'purchase_return')
                    ->where('reference_id', $purchaseReturn->id)
                    ->delete();
            }

            $purchaseReturn->items()->delete();
            $purchaseReturn->forceDelete();
        });

        return redirect()->route('purchase-returns.index')
            ->with('success', 'Purchase return and related accounting records deleted successfully.');
    }

    public function approve(PurchaseReturn $purchaseReturn)
    {
        $this->authorizePermission('approve purchase-returns');
        if ($purchaseReturn->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be approved.');
        }

        $purchaseReturn->approve();

        return back()->with('success', 'Purchase return approved successfully.');
    }

    public function complete(PurchaseReturn $purchaseReturn)
    {
        $this->authorizePermission('complete purchase-returns');
        if ($purchaseReturn->status !== 'approved') {
            return back()->with('error', 'Return must be approved before completion.');
        }

        try {
            DB::transaction(function () use ($purchaseReturn) {
                $purchaseReturn->complete();

                // Link to invoice & payment: create refund (credit from supplier) so purchase paid/due update
                $purchase = $purchaseReturn->purchase;
                $refundAmount = (float) $purchaseReturn->total_amount;
                if ($purchase && $refundAmount > 0) {
                    Payment::create([
                        'payment_type' => 'supplier',
                        'purchase_id' => $purchase->id,
                        'supplier_id' => $purchase->supplier_id,
                        'amount' => -$refundAmount,
                        'payment_date' => $purchaseReturn->return_date,
                        'payment_method' => settings('payments.default_payment_method', 'cash'),
                        'notes' => 'Credit – Purchase Return ' . $purchaseReturn->return_number,
                    ]);
                }

                AccountingService::recordPurchaseReturn($purchaseReturn);
            });
            $purchaseReturn->refresh();
            \App\Services\SmsNotificationService::purchaseReturnCompleted($purchaseReturn);
            return back()->with('success', 'Purchase return completed. Stock, invoice and accounting updated.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
