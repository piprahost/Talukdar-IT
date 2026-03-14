<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payment_number',
        'payment_type',
        'sale_id',
        'purchase_id',
        'customer_id',
        'supplier_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $prefix = $payment->payment_type === 'customer' ? 'CP' : 'SP';
                $payment->payment_number = $prefix . '-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
            if (auth()->check() && empty($payment->created_by)) {
                $payment->created_by = auth()->id();
            }
        });

        static::created(function ($payment) {
            $payment->updateRelatedPaymentStatus();
        });

        static::updated(function ($payment) {
            if ($payment->isDirty(['amount'])) {
                $payment->updateRelatedPaymentStatus();
            }
        });

        static::deleted(function ($payment) {
            $payment->updateRelatedPaymentStatus();
        });
    }

    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper Methods
    public function updateRelatedPaymentStatus()
    {
        if ($this->payment_type === 'customer' && $this->sale_id) {
            $sale = $this->sale;
            if ($sale) {
                $paidAmount = static::where('sale_id', $sale->id)
                    ->where('payment_type', 'customer')
                    ->sum('amount');

                $sale->paid_amount = $paidAmount;
                $sale->due_amount = max(0, $sale->total_amount - $paidAmount);
                $sale->payment_status = $sale->due_amount <= 0 && $sale->paid_amount > 0
                    ? 'paid'
                    : ($sale->paid_amount > 0 && $sale->due_amount > 0 ? 'partial' : 'unpaid');
                $sale->save();
            }
        } elseif ($this->payment_type === 'supplier' && $this->purchase_id) {
            $purchase = $this->purchase;
            if ($purchase) {
                $paidAmount = static::where('purchase_id', $purchase->id)
                    ->where('payment_type', 'supplier')
                    ->sum('amount');

                $purchase->paid_amount = $paidAmount;
                $purchase->due_amount = max(0, $purchase->total_amount - $paidAmount);
                $purchase->payment_status = $purchase->due_amount <= 0 && $purchase->paid_amount > 0
                    ? 'paid'
                    : ($purchase->paid_amount > 0 && $purchase->due_amount > 0 ? 'partial' : 'unpaid');
                $purchase->save();
            }
        }
    }

    public function getPaymentMethodNameAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'card' => 'Card',
            'mobile_banking' => 'Mobile Banking',
            'bank_transfer' => 'Bank Transfer',
            'cheque' => 'Cheque',
            'other' => 'Other',
            default => 'Cash',
        };
    }

    /** Eager-load relations needed for list/detail views (connected data). */
    public function scopeWithStandardRelations($query)
    {
        return $query->with(self::getStandardRelations());
    }

    /** Relation names for list/detail (single source of truth). */
    public static function getStandardRelations(): array
    {
        return [
            'sale:id,invoice_number,sale_date,total_amount,paid_amount,due_amount',
            'purchase:id,po_number,order_date,total_amount,paid_amount,due_amount',
            'customer',
            'supplier',
            'creator',
        ];
    }
}
