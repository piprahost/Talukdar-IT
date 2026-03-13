<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Warranty extends Model
{
    protected $fillable = [
        'sale_item_id',
        'product_id',
        'sale_id',
        'customer_id',
        'barcode',
        'warranty_period_days',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'warranty_period_days' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($warranty) {
            // Auto-update status based on end date
            if ($warranty->end_date && $warranty->end_date->isPast() && $warranty->status === 'active') {
                $warranty->status = 'expired';
            }
        });
    }

    // Relationships
    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function submissions()
    {
        return $this->hasMany(WarrantySubmission::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('end_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere('end_date', '<', now());
        });
    }

    public function scopeByBarcode($query, $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    // Helper Methods
    public function isActive()
    {
        return $this->status === 'active' && $this->end_date->isFuture();
    }

    public function isExpired()
    {
        return $this->status === 'expired' || $this->end_date->isPast();
    }

    public function daysRemaining()
    {
        if ($this->isExpired()) {
            return 0;
        }
        
        // Use floor to ensure whole days (no decimal places)
        return max(0, (int) floor(now()->diffInDays($this->end_date, false)));
    }

    public function daysExpired()
    {
        if (!$this->isExpired()) {
            return 0;
        }
        
        // Use floor to ensure whole days (no decimal places)
        return (int) floor(abs(now()->diffInDays($this->end_date, false)));
    }

    public function getProgressPercentage()
    {
        $totalDays = $this->warranty_period_days;
        $elapsedDays = now()->diffInDays($this->start_date);
        
        if ($elapsedDays >= $totalDays) {
            return 100;
        }
        
        return ($elapsedDays / $totalDays) * 100;
    }
}
