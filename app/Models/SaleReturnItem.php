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
            $barcode = trim((string) ($this->barcode ?? ''));
            if ($barcode === '' && $this->sale_item_id) {
                if (!$this->relationLoaded('saleItem')) {
                    $this->load('saleItem');
                }
                $barcode = trim((string) ($this->saleItem->barcode ?? ''));
            }

            $notes = "Sale Return: {$this->saleReturn->return_number}" . ($barcode !== '' ? " - Barcode: {$barcode}" : '');

            if ($barcode !== '') {
                $this->product->addBarcode($barcode, true, $notes);
            } else {
                $this->product->addStock(
                    (int) $this->quantity,
                    'in',
                    $notes,
                    'sale_return',
                    $this->sale_return_id
                );
            }
        }
    }
}
