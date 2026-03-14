<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = [
        'service_number',
        'product_name',
        'serial_number',
        'problem_notes',
        'service_notes',
        'service_cost',
        'receive_date',
        'delivery_date',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'paid_amount',
        'due_amount',
        'payment_method',
        'bank_account_id',
        'status',
        'internal_notes',
        'created_by',
    ];

    protected $casts = [
        'receive_date' => 'date',
        'delivery_date' => 'date',
        'service_cost' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    /**
     * Get the user who created this service
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function returns()
    {
        return $this->hasMany(ServiceReturn::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    /**
     * Optional link to customer (customer_name/phone/address are still stored as snapshot).
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /** Relation names for show/detail (connected data). */
    public static function getStandardRelations(): array
    {
        return ['creator', 'customer', 'returns', 'bankAccount'];
    }

    /** Eager-load relations for list/detail views. */
    public function scopeWithStandardRelations($query)
    {
        return $query->with(self::getStandardRelations());
    }

    /** Display name: from customer relation when linked, else snapshot. */
    public function getDisplayCustomerNameAttribute(): string
    {
        return $this->customer_id && $this->relationLoaded('customer') && $this->customer
            ? $this->customer->name
            : (string) $this->customer_name;
    }

    /**
     * Payment status: fully_paid | partial | unpaid
     */
    public function getPaymentStatusAttribute(): string
    {
        $cost = (float) $this->service_cost;
        $paid = (float) $this->paid_amount;
        $due  = (float) $this->due_amount;

        if ($cost == 0 || $due == 0) {
            return 'fully_paid';
        }
        if ($paid > 0 && $due > 0) {
            return 'partial';
        }
        return 'unpaid';
    }

    /**
     * Human-readable payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        $labels = [
            'cash'          => 'Cash',
            'card'          => 'Card',
            'mobile_banking'=> 'Mobile Banking',
            'bank_transfer' => 'Bank Transfer',
            'cheque'        => 'Cheque',
            'other'         => 'Other',
        ];
        return $labels[$this->payment_method] ?? ucfirst($this->payment_method ?? 'cash');
    }

    /**
     * Generate unique service number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->service_number)) {
                $prefix = function_exists('settings') ? (settings('services.service_memo_prefix') ?: 'SRV-') : 'SRV-';
                $year = date('Y');
                $count = static::where('service_number', 'like', $prefix . $year . '-%')->count();
                $service->service_number = $prefix . $year . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
