<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'expense_number',
        'expense_date',
        'category',
        'account_id',
        'amount',
        'payment_method',
        'vendor_name',
        'vendor_contact',
        'description',
        'reference_number',
        'attachment',
        'bank_account_id',
        'status',
        'payment_date',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($expense) {
            if (empty($expense->expense_number)) {
                $date = date('Ymd');
                $count = static::whereDate('created_at', today())->count();
                $expense->expense_number = 'EXP-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            }
            if (auth()->check() && empty($expense->created_by)) {
                $expense->created_by = auth()->id();
            }
        });

        static::updated(function ($expense) {
            // Auto-create journal entry when expense is approved or paid
            if ($expense->isDirty('status')) {
                if (in_array($expense->status, ['approved', 'paid'])) {
                    \App\Services\AccountingService::recordExpense($expense);
                }
            }
        });

        static::created(function ($expense) {
            // Auto-create journal entry if expense is created as approved/paid
            if (in_array($expense->status, ['approved', 'paid'])) {
                \App\Services\AccountingService::recordExpense($expense);
            }
        });
    }

    // Relationships
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function journalEntry()
    {
        return JournalEntry::where('reference_type', self::class)
            ->where('reference_id', $this->id)
            ->first();
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeWithStandardRelations($query)
    {
        return $query->with(static::getStandardRelations());
    }

    /** Relation names for show/detail (connected data). */
    public static function getStandardRelations(): array
    {
        return ['account', 'bankAccount', 'creator', 'approver'];
    }

    // Helper Methods
    public function approve()
    {
        $this->status = 'approved';
        $this->approved_by = auth()->id();
        $this->approved_at = now();
        $this->save();
    }

    public function markAsPaid($paymentDate = null)
    {
        $this->status = 'paid';
        $this->payment_date = $paymentDate ?? now();
        $this->save();
    }

    public function cancel()
    {
        $this->status = 'cancelled';
        $this->save();
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'approved' => '<span class="badge bg-info">Approved</span>',
            'paid' => '<span class="badge bg-success">Paid</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>';
    }

    public function getPaymentMethodBadgeAttribute()
    {
        $methods = [
            'cash' => '<span class="badge bg-success">Cash</span>',
            'card' => '<span class="badge bg-primary">Card</span>',
            'mobile_banking' => '<span class="badge bg-info">Mobile Banking</span>',
            'bank_transfer' => '<span class="badge bg-primary">Bank Transfer</span>',
            'cheque' => '<span class="badge bg-warning">Cheque</span>',
            'other' => '<span class="badge bg-secondary">Other</span>',
        ];

        return $methods[$this->payment_method] ?? '<span class="badge bg-secondary">' . ucfirst(str_replace('_', ' ', $this->payment_method)) . '</span>';
    }
}
