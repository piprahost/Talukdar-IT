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
     * Generate unique service number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->service_number)) {
                $year = date('Y');
                $count = static::where('service_number', 'like', 'SRV-' . $year . '-%')->count();
                $service->service_number = 'SRV-' . $year . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
