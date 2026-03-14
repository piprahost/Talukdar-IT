<?php

namespace App\Http\Controllers;

use App\Models\SaleReturn;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturnItem;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view sale-returns');
        $query = SaleReturn::with(['sale', 'customer', 'creator'])->latest();

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('return_number', 'like', "%{$request->search}%")
                  ->orWhereHas('sale', function($sq) use ($request) {
                      $sq->where('invoice_number', 'like', "%{$request->search}%");
                  })
                  ->orWhere('customer_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $returns = $query->paginate(15)->appends($request->query());
        
        return view('returns.sale-returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $this->authorizePermission('create sale-returns');
        $saleId = $request->get('sale_id');
        $sale = null;
        
        if ($saleId) {
            $sale = Sale::where('status', 'completed')
                ->with(['items.product', 'customer'])
                ->findOrFail($saleId);
        }
        
        $sales = Sale::where('status', 'completed')
            ->with('customer')
            ->latest()
            ->get();
        
        return view('returns.sale-returns.create', compact('sale', 'sales'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create sale-returns');
        $validated = $request->validate([
            'sale_id' => ['required', 'exists:sales,id'],
            'return_date' => ['required', 'date'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sale_item_id' => ['required', 'exists:sale_items,id'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.barcode' => ['nullable', 'string'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.reason' => ['nullable', 'string'],
        ]);

        $sale = Sale::findOrFail($validated['sale_id']);

        // Validate return quantity does not exceed (original - already returned) per line
        foreach ($validated['items'] as $itemData) {
            $saleItem = SaleItem::find($itemData['sale_item_id']);
            if (!$saleItem || $saleItem->sale_id != $sale->id) {
                return back()->withErrors(['items' => 'Invalid sale item for this invoice.'])->withInput();
            }
            $alreadyReturned = (int) SaleReturnItem::where('sale_item_id', $saleItem->id)
                ->whereHas('saleReturn', fn ($q) => $q->where('sale_id', $sale->id))
                ->sum('quantity');
            $maxReturnable = $saleItem->quantity - $alreadyReturned;
            if ($itemData['quantity'] > $maxReturnable) {
                return back()->withErrors(['items' => "Return quantity for product cannot exceed {$maxReturnable} (sold: {$saleItem->quantity}, already returned: {$alreadyReturned})."])->withInput();
            }
        }

        $return = DB::transaction(function () use ($validated, $sale) {
            $return = SaleReturn::create([
                'sale_id' => $validated['sale_id'],
                'customer_id' => $sale->customer_id,
                'return_date' => $validated['return_date'],
                'reason' => $validated['reason'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'status' => 'pending',
            ]);

            foreach ($validated['items'] as $itemData) {
                $return->items()->create([
                    'sale_item_id' => $itemData['sale_item_id'],
                    'product_id' => $itemData['product_id'],
                    'barcode' => $itemData['barcode'] ?? null,
                    'unit_price' => $itemData['unit_price'],
                    'discount' => $itemData['discount'] ?? 0,
                    'quantity' => $itemData['quantity'],
                    'reason' => $itemData['reason'] ?? null,
                ]);
            }

            $return->calculateTotals();
            
            return $return;
        });

        return redirect()->route('sale-returns.show', $return)
            ->with('success', 'Sale return created successfully.');
    }

    public function show(SaleReturn $saleReturn)
    {
        $this->authorizePermission('view sale-returns');
        $saleReturn->load(['sale.customer', 'customer', 'items.product', 'items.saleItem', 'creator', 'approver']);
        return view('returns.sale-returns.show', compact('saleReturn'));
    }

    public function edit(SaleReturn $saleReturn)
    {
        $this->authorizePermission('edit sale-returns');
        if ($saleReturn->status !== 'pending') {
            return redirect()->route('sale-returns.show', $saleReturn)
                ->with('error', 'Only pending returns can be edited.');
        }

        $saleReturn->load(['sale.items.product', 'items.product', 'items.saleItem']);
        return view('returns.sale-returns.edit', compact('saleReturn'));
    }

    public function update(Request $request, SaleReturn $saleReturn)
    {
        $this->authorizePermission('edit sale-returns');
        if ($saleReturn->status !== 'pending') {
            return redirect()->route('sale-returns.show', $saleReturn)
                ->with('error', 'Only pending returns can be edited.');
        }

        $validated = $request->validate([
            'return_date' => ['required', 'date'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sale_item_id' => ['required', 'exists:sale_items,id'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.barcode' => ['nullable', 'string'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.reason' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($saleReturn, $validated) {
            $saleReturn->update([
                'return_date' => $validated['return_date'],
                'reason' => $validated['reason'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
            ]);

            $saleReturn->items()->delete();

            foreach ($validated['items'] as $itemData) {
                $saleReturn->items()->create([
                    'sale_item_id' => $itemData['sale_item_id'],
                    'product_id' => $itemData['product_id'],
                    'barcode' => $itemData['barcode'] ?? null,
                    'unit_price' => $itemData['unit_price'],
                    'discount' => $itemData['discount'] ?? 0,
                    'quantity' => $itemData['quantity'],
                    'reason' => $itemData['reason'] ?? null,
                ]);
            }

            $saleReturn->calculateTotals();
        });

        return redirect()->route('sale-returns.show', $saleReturn)
            ->with('success', 'Sale return updated successfully.');
    }

    public function destroy(SaleReturn $saleReturn)
    {
        $this->authorizePermission('delete sale-returns');
        if ($saleReturn->status !== 'pending') {
            return redirect()->route('sale-returns.index')
                ->with('error', 'Only pending returns can be deleted.');
        }

        $saleReturn->delete();

        return redirect()->route('sale-returns.index')
            ->with('success', 'Sale return deleted successfully.');
    }

    public function approve(SaleReturn $saleReturn)
    {
        $this->authorizePermission('approve sale-returns');
        if ($saleReturn->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be approved.');
        }

        $saleReturn->approve();

        return back()->with('success', 'Sale return approved successfully.');
    }

    public function complete(SaleReturn $saleReturn)
    {
        $this->authorizePermission('complete sale-returns');
        if ($saleReturn->status !== 'approved') {
            return back()->with('error', 'Return must be approved before completion.');
        }

        try {
            $saleReturn->complete();
            // Create journal entry for completed return
            AccountingService::recordSaleReturn($saleReturn);
            return back()->with('success', 'Sale return completed. Stock updated.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
