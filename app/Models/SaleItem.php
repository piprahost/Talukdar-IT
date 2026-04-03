<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Warranty;
use Carbon\Carbon;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'barcode',
        'unit_price',
        'discount',
        'quantity',
        'subtotal',
        'purchase_unit_cost',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
        'purchase_unit_cost' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if ($item->purchase_unit_cost === null && $item->barcode && $item->product_id) {
                $pi = PurchaseItem::latestReceivedForBarcode((int) $item->product_id, (string) $item->barcode);
                if ($pi) {
                    $item->purchase_unit_cost = $pi->cost_price;
                }
            }
        });

        static::saving(function ($item) {
            // Calculate subtotal
            $item->subtotal = ($item->unit_price * $item->quantity) - $item->discount;
        });

        static::created(function ($item) {
            // Update product stock when item is created (sale completed)
            if ($item->sale && $item->sale->status === 'completed') {
                $item->updateProductStock();
                $item->createWarranty();
            }
        });

        static::updated(function ($item) {
            // Update product stock when sale status changes to completed
            if ($item->isDirty() && $item->sale && $item->sale->status === 'completed') {
                $item->updateProductStock();
                // Create warranty if sale just completed and warranty doesn't exist
                if (!$item->warranty()->exists()) {
                    $item->createWarranty();
                }
            }
        });
    }

    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warranty()
    {
        return $this->hasOne(Warranty::class);
    }

    // Helper Methods
    public function updateProductStock()
    {
        // Check sale status first
        if (!$this->relationLoaded('sale')) {
            $this->load('sale');
        }
        
        if (!$this->sale || $this->sale->status !== 'completed') {
            return;
        }
        
        // Eager load product if not already loaded
        if (!$this->relationLoaded('product')) {
            $this->load('product');
        }
        
        if (!$this->product) {
            return;
        }
        
        // If barcode is specified and exists in product, remove it (1 barcode = 1 stock unit)
        if ($this->barcode && $this->product->hasBarcode($this->barcode)) {
            $this->product->removeBarcode($this->barcode);
        } else {
            // Reduce stock by quantity (for items without specific barcode)
            $barcode = $this->barcode ?? 'N/A';
            $invoiceNumber = $this->sale->invoice_number ?? 'N/A';
            $this->product->reduceStock(
                $this->quantity,
                'out',
                "Sale: {$invoiceNumber} - Item: {$barcode}",
                'sale',
                $this->sale_id
            );
        }
    }

    /**
     * Create warranty for this sale item if product has warranty period
     */
    public function createWarranty()
    {
        // Load product if not already loaded
        if (!$this->relationLoaded('product')) {
            $this->load('product');
        }
        
        if (!$this->product || !$this->product->warranty_period || $this->product->warranty_period <= 0) {
            return; // No warranty period set
        }
        
        // Load sale if not already loaded
        if (!$this->relationLoaded('sale')) {
            $this->load('sale');
        }
        
        if (!$this->sale) {
            return;
        }
        
        // Calculate warranty dates
        // Warranty starts from the sale date (not creation date or current date)
        $startDate = $this->sale->sale_date ?? now();
        
        // Ensure start_date is a Carbon instance
        if (!$startDate instanceof \Carbon\Carbon) {
            $startDate = \Carbon\Carbon::parse($startDate);
        }
        
        // Calculate end date by adding warranty period days to sale date
        $endDate = $startDate->copy()->addDays($this->product->warranty_period);
        
        // Create warranty record - warranty starts from sale date
        Warranty::create([
            'sale_item_id' => $this->id,
            'product_id' => $this->product_id,
            'sale_id' => $this->sale_id,
            'customer_id' => $this->sale->customer_id,
            'barcode' => $this->barcode,
            'warranty_period_days' => $this->product->warranty_period,
            'start_date' => $startDate->format('Y-m-d'), // Use sale date as start date
            'end_date' => $endDate->format('Y-m-d'),
            'status' => 'active',
        ]);
    }
}
