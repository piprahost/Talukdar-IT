<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $fillable = [
        'purchase_return_id',
        'purchase_item_id',
        'product_id',
        'barcode',
        'cost_price',
        'quantity',
        'subtotal',
        'reason',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->subtotal = $item->cost_price * $item->quantity;
        });

        static::created(function ($item) {
            if ($item->purchaseReturn->status === 'completed') {
                $item->updateProductStock();
            }
        });

        static::updated(function ($item) {
            if ($item->purchaseReturn->status === 'completed' && $item->isDirty(['quantity'])) {
                $item->updateProductStock();
            }
        });
    }

    // Relationships
    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helper Methods
    public function updateProductStock()
    {
        if (!$this->relationLoaded('product')) {
            $this->load('product');
        }
        if (!$this->relationLoaded('purchaseReturn')) {
            $this->load('purchaseReturn');
        }

        if ($this->product && $this->purchaseReturn->status === 'completed') {
            $notes = "Purchase Return: {$this->purchaseReturn->return_number}" . ($this->barcode ? " - Barcode: {$this->barcode}" : "");
            
            // Reduce stock when returning purchased items
            $this->product->reduceStock(
                $this->quantity,
                'out',
                $notes,
                'purchase_return',
                $this->purchase_return_id
            );

            // Remove barcode if specified
            if ($this->barcode && $this->product->hasBarcode($this->barcode)) {
                $this->product->removeBarcode($this->barcode);
            }
        }
    }
}
