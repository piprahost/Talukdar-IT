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
            'type' => ['required', 'in:in,out,adjustment'],
            'barcodes' => ['nullable', 'string'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'target_stock' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $notes = $validated['notes'] ?? null;
        $type = $validated['type'];

        $parseBarcodes = function ($input) {
            if (empty(trim((string) $input))) {
                return [];
            }
            $parts = preg_split('/[\r\n,]+/', $input, -1, PREG_SPLIT_NO_EMPTY);
            return array_values(array_unique(array_map('trim', array_filter($parts))));
        };

        if ($type === 'in') {
            $barcodes = $parseBarcodes($validated['barcodes'] ?? '');
            if (count($barcodes) === 0) {
                return back()->with('error', 'Enter at least one barcode. Each barcode = 1 stock unit.');
            }
            $added = $product->addBarcodes($barcodes, true, $notes);
            $skipped = count($barcodes) - $added;
            $message = $added > 0
                ? "Added {$added} unit(s) with barcode(s)."
                : "No new units added.";
            if ($skipped > 0) {
                $message .= " {$skipped} barcode(s) already exist and were skipped.";
            }
            return back()->with('success', $message);
        }

        if ($type === 'out') {
            $barcodes = $parseBarcodes($validated['barcodes'] ?? '');
            $quantity = isset($validated['quantity']) ? (int) $validated['quantity'] : null;
            $currentStock = $product->stock_quantity;
            $barcodeCount = $product->getBarcodesCount();

            if ($barcodeCount > 0) {
                if (count($barcodes) > 0) {
                    $removed = $product->removeBarcodes($barcodes, $notes);
                    if ($removed === 0) {
                        return back()->with('error', 'None of the entered barcodes belong to this product.');
                    }
                    return back()->with('success', "Removed {$removed} unit(s) (barcodes).");
                }
                if ($quantity !== null && $quantity > 0) {
                    if ($quantity > $barcodeCount) {
                        return back()->with('error', "Insufficient barcodes. Product has {$barcodeCount} barcode(s).");
                    }
                    $toRemove = array_slice($product->barcodes ?? [], -$quantity);
                    $product->removeBarcodes($toRemove, $notes ?: "Removed last {$quantity} unit(s).");
                    return back()->with('success', "Removed {$quantity} unit(s).");
                }
                return back()->with('error', 'Enter barcodes to remove (one per line) or quantity to remove.');
            }

            if ($quantity !== null && $quantity > 0) {
                if ($quantity > $currentStock) {
                    return back()->with('error', "Insufficient stock. Available: {$currentStock}");
                }
                $product->reduceStock($quantity, 'out', $notes);
                return back()->with('success', "Removed {$quantity} unit(s).");
            }
            return back()->with('error', 'Enter quantity to remove.');
        }

        // adjustment: set stock to target (1 barcode = 1 unit)
        $target = isset($validated['target_stock']) ? (int) $validated['target_stock'] : null;
        if ($target === null) {
            return back()->with('error', 'Enter target stock quantity.');
        }
        $current = $product->stock_quantity;
        $diff = $target - $current;
        if ($diff === 0) {
            return back()->with('info', 'Stock is already at target.');
        }
        if ($diff > 0) {
            $barcodes = $parseBarcodes($validated['barcodes'] ?? '');
            if (count($barcodes) < $diff) {
                return back()->with('error', "Target is {$diff} unit(s) higher. Enter {$diff} barcode(s) (one per line).");
            }
            $barcodes = array_slice($barcodes, 0, $diff);
            $product->addBarcodes($barcodes, true, $notes ?: 'Manual adjustment');
            return back()->with('success', "Stock adjusted: added " . count($barcodes) . " unit(s).");
        }
        $toRemoveCount = abs($diff);
        $barcodeCount = $product->getBarcodesCount();
        if ($barcodeCount > 0) {
            $barcodes = $parseBarcodes($validated['barcodes'] ?? '');
            if (count($barcodes) >= $toRemoveCount) {
                $product->removeBarcodes(array_slice($barcodes, 0, $toRemoveCount), $notes ?: 'Manual adjustment');
            } else {
                $toRemove = array_slice($product->barcodes ?? [], -$toRemoveCount);
                $product->removeBarcodes($toRemove, $notes ?: 'Manual adjustment');
            }
        } else {
            $product->reduceStock($toRemoveCount, 'adjustment', $notes);
        }
        return back()->with('success', "Stock adjusted: removed {$toRemoveCount} unit(s).");
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
