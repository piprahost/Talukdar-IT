<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'parent_id',
        'opening_balance',
        'balance_type',
        'description',
        'is_active',
        'is_system',
        'sort_order',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalEntryItems()
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRootAccounts($query)
    {
        return $query->whereNull('parent_id');
    }

    // Helper Methods
    public function getCurrentBalanceAttribute()
    {
        $debitTotal = $this->journalEntryItems()
            ->join('journal_entries', 'journal_entry_items.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.status', 'posted')
            ->whereNull('journal_entries.deleted_at')
            ->sum('journal_entry_items.debit');

        $creditTotal = $this->journalEntryItems()
            ->join('journal_entries', 'journal_entry_items.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.status', 'posted')
            ->whereNull('journal_entries.deleted_at')
            ->sum('journal_entry_items.credit');

        $openingBalance = $this->opening_balance;

        if ($this->balance_type === 'debit') {
            return ($openingBalance + $debitTotal) - $creditTotal;
        } else {
            return ($openingBalance + $creditTotal) - $debitTotal;
        }
    }

    public function getBalanceAsOfDate($date)
    {
        $debitTotal = $this->journalEntryItems()
            ->join('journal_entries', 'journal_entry_items.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.status', 'posted')
            ->where('journal_entries.entry_date', '<=', $date)
            ->whereNull('journal_entries.deleted_at')
            ->sum('journal_entry_items.debit');

        $creditTotal = $this->journalEntryItems()
            ->join('journal_entries', 'journal_entry_items.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.status', 'posted')
            ->where('journal_entries.entry_date', '<=', $date)
            ->whereNull('journal_entries.deleted_at')
            ->sum('journal_entry_items.credit');

        $openingBalance = $this->opening_balance;

        if ($this->balance_type === 'debit') {
            return ($openingBalance + $debitTotal) - $creditTotal;
        } else {
            return ($openingBalance + $creditTotal) - $debitTotal;
        }
    }
}
