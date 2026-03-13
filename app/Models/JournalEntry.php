<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JournalEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'entry_number',
        'entry_date',
        'description',
        'reference',
        'reference_type',
        'reference_id',
        'status',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'posted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($entry) {
            if (empty($entry->entry_number)) {
                $date = date('Ymd');
                $count = static::whereDate('created_at', today())->count();
                $entry->entry_number = 'JE-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            }
            if (auth()->check() && empty($entry->created_by)) {
                $entry->created_by = auth()->id();
            }
        });
    }

    // Relationships
    public function items()
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    // Scopes
    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Helper Methods
    public function getTotalDebitAttribute()
    {
        return $this->items->sum('debit');
    }

    public function getTotalCreditAttribute()
    {
        return $this->items->sum('credit');
    }

    public function isBalanced()
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    public function post()
    {
        if (!$this->isBalanced()) {
            throw new \Exception('Journal entry is not balanced. Debit and Credit totals must match.');
        }

        $this->status = 'posted';
        $this->posted_by = auth()->id();
        $this->posted_at = now();
        $this->save();
    }

    public function unpost()
    {
        $this->status = 'draft';
        $this->posted_by = null;
        $this->posted_at = null;
        $this->save();
    }
}
