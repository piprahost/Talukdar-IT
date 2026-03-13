<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'previous_stock',
        'current_stock',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'current_stock' => 'integer',
        'reference_id' => 'integer',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeIncoming($query)
    {
        return $query->whereIn('type', ['in', 'return', 'adjustment'])->where('quantity', '>', 0);
    }

    public function scopeOutgoing($query)
    {
        return $query->whereIn('type', ['out', 'sold', 'damaged'])->orWhere(function ($q) {
            $q->where('type', 'adjustment')->where('quantity', '<', 0);
        });
    }
}
