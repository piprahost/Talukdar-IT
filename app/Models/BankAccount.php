<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_name',
        'bank_name',
        'account_number',
        'branch_name',
        'routing_number',
        'swift_code',
        'account_type',
        'opening_balance',
        'current_balance',
        'account_id',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function updateBalance()
    {
        if ($this->account_id) {
            $account = Account::find($this->account_id);
            if ($account) {
                $this->current_balance = $account->current_balance;
                $this->save();
            }
        }
    }
}
