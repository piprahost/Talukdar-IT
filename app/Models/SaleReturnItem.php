<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturnItem extends Model
{
    protected $fillable = [
        'sale_return_id',
        'sale_item_id',
        'product_id',
        'barcode',
        'unit_price',
        'discount',
        'quantity',
        'subtotal',
        'reason',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->subtotal = ($item->unit_price * $item->quantity) - $item->discount;
        });

        static::created(function ($item) {
            if ($item->saleReturn->status === 'completed') {
                $item->updateProductStock();
            }
        });

        static::updated(function ($item) {
            if ($item->saleReturn->status === 'completed' && $item->isDirty(['quantity'])) {
                $item->updateProductStock();
            }
        });
    }

    // Relationships
    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
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
        if (!$this->relationLoaded('saleReturn')) {
            $this->load('saleReturn');
        }

        if ($this->product && $this->saleReturn->status === 'completed') {
            $barcode = $this->barcode ?? 'N/A';
            $notes = "Sale Return: {$this->saleReturn->return_number}" . ($this->barcode ? " - Barcode: {$this->barcode}" : "");
            
            // Add stock back when returning sold items
            if ($this->barcode) {
                // If barcode exists, add it back to product barcodes and increment stock
                $this->product->addBarcode($this->barcode, true, $notes);
            } else {
                // If no barcode, just add quantity to stock
                $this->product->addStock(
                    $this->quantity,
                    'in',
                    $notes,
                    'sale_return',
                    $this->sale_return_id
                );
            }
        }
    }
}
