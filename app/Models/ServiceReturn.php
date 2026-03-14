<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ServiceReturn extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'return_number',
        'service_id',
        'return_date',
        'status',
        'reason',
        'notes',
        'refund_amount',
        'refund_status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'return_date' => 'date',
        'refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($return) {
            if (empty($return->return_number)) {
                $date = date('Ymd');
                $count = static::whereDate('created_at', today())->count();
                $return->return_number = 'SVR-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            }
            if (auth()->check() && empty($return->created_by)) {
                $return->created_by = auth()->id();
            }
        });
    }

    // Relationships
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** Relation names for show/detail. */
    public static function getStandardRelations(): array
    {
        return ['service.customer', 'service.creator', 'creator', 'approver'];
    }

    public function scopeWithStandardRelations($query)
    {
        return $query->with(self::getStandardRelations());
    }

    // Helper Methods
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
        $this->save();
    }

    public function processRefund()
    {
        if ($this->status !== 'completed') {
            throw new \Exception('Return must be completed before processing refund.');
        }
        $this->refund_status = 'processed';
        $this->save();
    }
}
