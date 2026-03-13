<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'sale_date',
        'due_date',
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
    ];

    protected $casts = [
        'sale_date' => 'date',
        'due_date' => 'date',
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

        static::creating(function ($sale) {
            if (empty($sale->invoice_number)) {
                $sale->invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
            if (auth()->check() && empty($sale->created_by)) {
                $sale->created_by = auth()->id();
            }
            // Calculate due amount
            if (!isset($sale->due_amount)) {
                $sale->due_amount = max(0, $sale->total_amount - ($sale->paid_amount ?? 0));
            }
        });

        static::updating(function ($sale) {
            // Recalculate due amount if totals changed
            if ($sale->isDirty(['total_amount', 'paid_amount'])) {
                $sale->due_amount = max(0, $sale->total_amount - $sale->paid_amount);
                
                // Update payment status based on due amount
                if ($sale->due_amount <= 0 && $sale->paid_amount > 0) {
                    $sale->payment_status = 'paid';
                } elseif ($sale->paid_amount > 0 && $sale->due_amount > 0) {
                    $sale->payment_status = 'partial';
                } else {
                    $sale->payment_status = 'unpaid';
                }
            }
        });

        static::updated(function ($sale) {
            // Create warranties when sale status changes to 'completed'
            if ($sale->isDirty('status') && $sale->status === 'completed') {
                // Load items if not already loaded
                if (!$sale->relationLoaded('items')) {
                    $sale->load('items');
                }
                
                // Create warranties for all items if they don't exist
                foreach ($sale->items as $item) {
                    if (!$item->warranty()->exists()) {
                        $item->createWarranty();
                    }
                }
            }
        });
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function warranties()
    {
        return $this->hasMany(Warranty::class);
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Helper Methods
    public function calculateTotals()
    {
        // Use database aggregation instead of loading all items into memory
        $this->subtotal = $this->items()->sum('subtotal') ?? 0;
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->due_amount = max(0, $this->total_amount - $this->paid_amount);
        
        // Update payment status based on due amount (already handled in boot, but ensure consistency)
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
