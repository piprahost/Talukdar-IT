<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view stock-movements');
        $query = StockMovement::with(['product', 'creator'])->latest();

        if ($request->has('product_id') && $request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(20)->appends($request->query());
        $products = Product::active()->orderBy('name')->get();

        return view('products.stock.index', compact('movements', 'products'));
    }

    public function adjustStock(Request $request, Product $product)
    {
        $this->authorizePermission('adjust stock');
        $validated = $request->validate([
            'quantity' => ['required', 'integer'],
            'type' => ['required', 'in:in,out,adjustment'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['type'] === 'in') {
            $product->addStock($validated['quantity'], 'in', $validated['notes'] ?? null);
        } elseif ($validated['type'] === 'out') {
            if ($product->stock_quantity < $validated['quantity']) {
                return back()->with('error', 'Insufficient stock. Available: ' . $product->stock_quantity);
            }
            $product->reduceStock($validated['quantity'], 'out', $validated['notes'] ?? null);
        } else {
            // Adjustment
            $difference = $validated['quantity'] - $product->stock_quantity;
            if ($difference > 0) {
                $product->addStock($difference, 'adjustment', $validated['notes'] ?? null);
            } elseif ($difference < 0) {
                $product->reduceStock(abs($difference), 'adjustment', $validated['notes'] ?? null);
            }
        }

        return back()->with('success', 'Stock updated successfully.');
    }

    public function lowStock()
    {
        $this->authorizePermission('view stock');
        $products = Product::with(['category', 'brand'])
            ->lowStock()
            ->active()
            ->orderBy('stock_quantity')
            ->paginate(20);

        return view('products.stock.low-stock', compact('products'));
    }

    public function createManual()
    {
        $this->authorizePermission('create stock');
        $products = Product::active()->orderBy('name')->get();
        return view('products.stock.create-manual', compact('products'));
    }

    public function storeManual(Request $request)
    {
        $this->authorizePermission('create stock');
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.barcode' => ['required', 'string'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        // Group items by product
        $itemsByProduct = [];
        foreach ($validated['items'] as $item) {
            $productId = $item['product_id'];
            if (!isset($itemsByProduct[$productId])) {
                $itemsByProduct[$productId] = [];
            }
            $itemsByProduct[$productId][] = $item['barcode'];
        }

        $totalAdded = 0;
        $duplicateBarcodes = [];
        
        \DB::transaction(function () use ($itemsByProduct, $validated, &$totalAdded, &$duplicateBarcodes) {
            // Process each product
            foreach ($itemsByProduct as $productId => $barcodes) {
                $product = Product::findOrFail($productId);
                $newBarcodes = [];
                
                // Process each barcode for this product
                foreach ($barcodes as $barcode) {
                    // Check if barcode already exists for this product
                    if ($product->hasBarcode($barcode)) {
                        $duplicateBarcodes[] = [
                            'product' => $product->name,
                            'barcode' => $barcode
                        ];
                        continue;
                    }
                    
                    // Add barcode to product (this also increments stock by 1 and creates stock movement)
                    $notes = "Manual stock entry" . (!empty($validated['notes']) ? " - " . $validated['notes'] : '');
                    if ($product->addBarcode($barcode, true, $notes)) {
                        $newBarcodes[] = $barcode;
                        $totalAdded++;
                    }
                }
            }
        });

        $message = $totalAdded > 0 
            ? "Successfully added " . $totalAdded . " stock unit(s) with barcode(s)." 
            : "No stock was added.";
            
        if (count($duplicateBarcodes) > 0) {
            $duplicateSummary = array_slice($duplicateBarcodes, 0, 3);
            $summaryParts = array_map(function($item) {
                return $item['product'] . ': ' . $item['barcode'];
            }, $duplicateSummary);
            $summaryText = implode(', ', $summaryParts);
            $message .= " Note: " . count($duplicateBarcodes) . " barcode(s) already exist and were skipped: " . $summaryText;
            if (count($duplicateBarcodes) > 3) {
                $message .= " and " . (count($duplicateBarcodes) - 3) . " more.";
            }
        }

        return redirect()->route('stock.index')
            ->with('success', $message);
    }
}
