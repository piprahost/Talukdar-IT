<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'barcodes',
        'category_id',
        'brand_id',
        'product_model_id',
        'description',
        'specifications',
        'unit',
        'cost_price',
        'selling_price',
        'discount_price',
        'stock_quantity',
        'reorder_level',
        'min_stock',
        'max_stock',
        'is_active',
        'is_featured',
        'status',
        'image',
        'gallery',
        'warranty_period',
        'notes',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'reorder_level' => 'integer',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'gallery' => 'array',
        'barcodes' => 'array', // Array of barcode strings
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // Auto-generate SKU if not provided
            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(Str::random(8));
            }
            // Set created_by if authenticated
            if (auth()->check() && empty($product->created_by)) {
                $product->created_by = auth()->id();
            }
            // Update status based on stock
            if ($product->stock_quantity <= 0) {
                $product->status = 'out_of_stock';
            }
        });

        static::updating(function ($product) {
            // Update status based on stock
            if ($product->stock_quantity <= 0) {
                $product->status = 'out_of_stock';
            } elseif ($product->stock_quantity > 0 && $product->status === 'out_of_stock') {
                $product->status = 'available';
            }
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function productModel()
    {
        return $this->belongsTo(ProductModel::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function warranties()
    {
        return $this->hasMany(Warranty::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors & Mutators
    /**
     * For shops where 1 barcode = 1 stock: show stock as barcode count when barcodes exist,
     * so display never shows more than actual traceable units.
     */
    public function getDisplayStockAttribute()
    {
        $barcodeCount = $this->getBarcodesCount();
        if ($barcodeCount > 0) {
            return $barcodeCount;
        }
        return (int) $this->stock_quantity;
    }

    /** Whether this product uses barcode-based stock (1 barcode = 1 unit). */
    public function getUsesBarcodeStockAttribute(): bool
    {
        return $this->getBarcodesCount() > 0;
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock_quantity <= $this->min_stock) {
            return 'low_stock';
        } elseif ($this->stock_quantity <= $this->reorder_level) {
            return 'reorder';
        }
        return 'in_stock';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0)->where('status', 'available');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'reorder_level');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhereJsonContains('barcodes', $search); // Search in barcodes array
        });
    }

    // Helper Methods
    public function addStock($quantity, $type = 'in', $notes = null, $referenceType = null, $referenceId = null)
    {
        $previousStock = $this->stock_quantity;
        $this->increment('stock_quantity', $quantity);
        $this->refresh();

        StockMovement::create([
            'product_id' => $this->id,
            'type' => $type,
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'current_stock' => $this->stock_quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);
    }

    public function reduceStock($quantity, $type = 'out', $notes = null, $referenceType = null, $referenceId = null)
    {
        $previousStock = $this->stock_quantity;
        $this->decrement('stock_quantity', $quantity);
        $this->refresh();

        StockMovement::create([
            'product_id' => $this->id,
            'type' => $type,
            'quantity' => -$quantity,
            'previous_stock' => $previousStock,
            'current_stock' => $this->stock_quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);
    }

    // Barcode Management Methods
    public function addBarcode($barcode, $updateStock = false, $stockNotes = null)
    {
        $barcodes = $this->barcodes ?? [];
        
        // Check if barcode already exists
        if (!in_array($barcode, $barcodes)) {
            $barcodes[] = $barcode;
            $this->barcodes = $barcodes;
            $this->save();
            
            // Optionally update stock (used when receiving purchase items)
            if ($updateStock) {
                $notes = $stockNotes ?? "Barcode added: {$barcode}";
                $this->addStock(1, 'in', $notes, 'barcode', null);
            }
            return true;
        }
        return false; // Barcode already exists
    }

    public function addBarcodes(array $barcodes, $updateStock = false, $stockNotes = null)
    {
        $existingBarcodes = $this->barcodes ?? [];
        $newBarcodes = [];
        
        foreach ($barcodes as $barcode) {
            if (!in_array($barcode, $existingBarcodes)) {
                $existingBarcodes[] = $barcode;
                $newBarcodes[] = $barcode;
            }
        }
        
        if (count($newBarcodes) > 0) {
            $this->barcodes = $existingBarcodes;
            $this->save();
            
            // Optionally update stock (used when receiving purchase items)
            if ($updateStock) {
                $notes = $stockNotes ?? "Barcodes added: " . implode(', ', $newBarcodes);
                $this->addStock(count($newBarcodes), 'in', $notes, 'barcode', null);
            }
            return count($newBarcodes);
        }
        return 0;
    }

    public function removeBarcode($barcode)
    {
        $barcodes = $this->barcodes ?? [];
        
        if (in_array($barcode, $barcodes)) {
            $barcodes = array_values(array_diff($barcodes, [$barcode]));
            $this->barcodes = $barcodes;
            $this->save();
            
            // Reduce stock by 1
            $this->reduceStock(1, 'out', "Barcode removed: {$barcode}", 'barcode', null);
            return true;
        }
        return false;
    }

    /**
     * Remove multiple barcodes and reduce stock once (1 barcode = 1 unit).
     * Returns number of barcodes actually removed.
     */
    public function removeBarcodes(array $barcodesToRemove, $notes = null): int
    {
        $current = $this->barcodes ?? [];
        $toRemove = array_values(array_intersect($current, $barcodesToRemove));
        if (count($toRemove) === 0) {
            return 0;
        }
        $newBarcodes = array_values(array_diff($current, $toRemove));
        $this->barcodes = $newBarcodes;
        $this->save();
        $count = count($toRemove);
        $notes = $notes ?? 'Barcodes removed: ' . implode(', ', array_slice($toRemove, 0, 5)) . (count($toRemove) > 5 ? '...' : '');
        $this->reduceStock($count, 'out', $notes, 'barcode', null);
        return $count;
    }

    public function hasBarcode($barcode)
    {
        $barcodes = $this->barcodes ?? [];
        return in_array($barcode, $barcodes);
    }

    public function getBarcodesCount()
    {
        return count($this->barcodes ?? []);
    }
}
