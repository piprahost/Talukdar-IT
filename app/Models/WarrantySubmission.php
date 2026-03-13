<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WarrantySubmission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'memo_number',
        'warranty_id',
        'sale_id',
        'product_id',
        'customer_id',
        'barcode',
        'submission_date',
        'problem_description',
        'customer_complaint',
        'condition',
        'physical_condition_notes',
        'customer_name',
        'customer_phone',
        'customer_address',
        'status',
        'internal_notes',
        'service_notes',
        'service_charge',
        'expected_completion_date',
        'completion_date',
        'return_date',
        'created_by',
        'assigned_to',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'expected_completion_date' => 'date',
        'completion_date' => 'date',
        'return_date' => 'date',
        'service_charge' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($submission) {
            if (empty($submission->memo_number)) {
                $submission->memo_number = 'WM-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
            if (auth()->check() && empty($submission->created_by)) {
                $submission->created_by = auth()->id();
            }
        });
    }

    // Relationships
    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Helper Methods
    public function isUnderWarranty()
    {
        return $this->warranty && $this->warranty->isActive();
    }

    public function getWarrantyStatus()
    {
        if (!$this->warranty) {
            return 'No Warranty';
        }
        
        if ($this->warranty->isActive()) {
            return 'Under Warranty';
        }
        
        return 'Warranty Expired';
    }
}
