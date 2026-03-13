<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Purchase extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'po_number',
        'supplier_id',
        'order_date',
        'expected_delivery_date',
        'received_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_method',
        'bank_account_id',
        'status',
        'payment_status',
        'notes',
        'internal_notes',
        'created_by',
        'received_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'received_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            if (empty($purchase->po_number)) {
                $purchase->po_number = 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
            if (auth()->check() && empty($purchase->created_by)) {
                $purchase->created_by = auth()->id();
            }
            // Calculate due amount
            if (!isset($purchase->due_amount)) {
                $purchase->due_amount = max(0, $purchase->total_amount - ($purchase->paid_amount ?? 0));
            }
        });

        static::updating(function ($purchase) {
            // Recalculate due amount if totals changed
            if ($purchase->isDirty(['total_amount', 'paid_amount'])) {
                $purchase->due_amount = max(0, $purchase->total_amount - $purchase->paid_amount);
                
                // Update payment status based on due amount
                if ($purchase->due_amount <= 0 && $purchase->paid_amount > 0) {
                    $purchase->payment_status = 'paid';
                } elseif ($purchase->paid_amount > 0 && $purchase->due_amount > 0) {
                    $purchase->payment_status = 'partial';
                } else {
                    $purchase->payment_status = 'unpaid';
                }
            }
        });
    }

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    // Helper Methods
    public function calculateTotals()
    {
        // Use database aggregation instead of loading all items into memory
        $result = $this->items()
            ->selectRaw('SUM(cost_price * quantity) as total')
            ->first();
        
        $this->subtotal = $result ? (float) $result->total : 0;
        
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->due_amount = max(0, $this->total_amount - $this->paid_amount);
        
        // Update payment status based on due amount
        if ($this->due_amount <= 0 && $this->paid_amount > 0) {
            $this->payment_status = 'paid';
        } elseif ($this->paid_amount > 0 && $this->due_amount > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }
        
        $this->save();
    }
}
