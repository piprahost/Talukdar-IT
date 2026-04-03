<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'barcode',
        'serial_number',
        'cost_price',
        'selling_price',
        'quantity',
        'status',
        'received_date',
        'condition_notes',
        'warranty_info',
        'notes',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'quantity' => 'integer',
        'received_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            // Update product stock when item is received
            if ($item->status === 'received') {
                $item->updateProductStock();
            }
        });

        static::updated(function ($item) {
            // Update product stock when status changes to received
            if ($item->isDirty('status') && $item->status === 'received') {
                $item->updateProductStock();
            }
        });
    }

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(Purchase::class, 'purchase_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Latest received purchase line for this product + barcode (1 barcode = 1 unit).
     */
    public static function latestReceivedForBarcode(int $productId, string $barcode): ?self
    {
        $barcode = trim($barcode);
        if ($barcode === '') {
            return null;
        }

        return static::query()
            ->where('product_id', $productId)
            ->where('barcode', $barcode)
            ->where('status', 'received')
            ->orderByDesc('id')
            ->first();
    }

    // Helper Methods
    public function updateProductStock()
    {
        if ($this->status !== 'received') {
            return;
        }
        
        // Eager load relationships if not already loaded to avoid N+1
        if (!$this->relationLoaded('product')) {
            $this->load('product');
        }
        
        if (!$this->product) {
            return;
        }
        
        // Lazy load purchase order only if needed
        $poNumber = $this->purchaseOrder->po_number ?? 'N/A';
        $notes = "Purchase: {$poNumber} - Barcode: {$this->barcode}";
        
        if ($this->barcode && !$this->product->hasBarcode($this->barcode)) {
            // Add barcode to product and update stock (1 barcode = 1 stock unit)
            $this->product->addBarcode($this->barcode, true, $notes);
        } else {
            // Barcode already exists or no barcode, just update stock
            $this->product->addStock($this->quantity, 'in', $notes, 'purchase', $this->purchase_order_id);
        }
    }

    public function markAsReceived($userId = null)
    {
        $this->status = 'received';
        $this->received_date = now();
        $this->save();
        
        if ($this->purchaseOrder && !$this->purchaseOrder->received_by) {
            $this->purchaseOrder->received_by = $userId ?? auth()->id();
            $this->purchaseOrder->received_date = now();
            $this->purchaseOrder->save();
        }

        // Check if all items are received
        $allReceived = $this->purchaseOrder->items()->where('status', '!=', 'received')->count() === 0;
        if ($allReceived) {
            $this->purchaseOrder->status = 'received';
            $this->purchaseOrder->save();
        } else {
            $this->purchaseOrder->status = 'partial';
            $this->purchaseOrder->save();
        }

        // Add barcode to product and update stock
        $this->updateProductStock();
    }
}
