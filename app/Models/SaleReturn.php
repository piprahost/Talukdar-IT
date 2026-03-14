<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SaleReturn extends Model
{
    use SoftDeletes;

    protected $table = 'sales_returns';

    protected $fillable = [
        'return_number',
        'sale_id',
        'customer_id',
        'return_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'reason',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'return_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($return) {
            if (empty($return->return_number)) {
                $date = date('Ymd');
                $count = static::whereDate('created_at', today())->count();
                $return->return_number = 'SR-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            }
            if (auth()->check() && empty($return->created_by)) {
                $return->created_by = auth()->id();
            }
        });

        static::created(function ($return) {
            $return->calculateTotals();
        });

        static::updated(function ($return) {
            if ($return->isDirty(['subtotal', 'tax_amount', 'discount_amount'])) {
                $return->calculateTotals();
            }
        });
    }

    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** Relation names for show/detail (single source of truth). */
    public static function getStandardRelations(): array
    {
        return ['sale.customer', 'customer', 'items.product', 'items.saleItem', 'creator', 'approver'];
    }

    public function scopeWithStandardRelations($query)
    {
        return $query->with(self::getStandardRelations());
    }

    // Helper Methods
    public function calculateTotals()
    {
        $this->subtotal = $this->items()->sum('subtotal');
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->save();
    }

    public function updateStock()
    {
        if ($this->status === 'completed') {
            foreach ($this->items as $item) {
                $item->updateProductStock();
            }
        }
    }

    public function approve()
    {
        $this->status = 'approved';
        $this->approved_by = auth()->id();
        $this->approved_at = now();
        $this->save();
    }

    public function complete()
    {
        if ($this->status !== 'approved') {
            throw new \Exception('Return must be approved before completion.');
        }
        $this->status = 'completed';
        $this->updateStock();
        $this->save();
    }
}
