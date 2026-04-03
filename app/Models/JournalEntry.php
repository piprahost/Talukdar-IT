<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JournalEntry extends Model
{
    use SoftDeletes;

    protected static ?bool $journalEntrySequencesTableExists = null;

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
                $entry->entry_number = static::generateNextEntryNumber();
            }
            if (auth()->check() && empty($entry->created_by)) {
                $entry->created_by = auth()->id();
            }
        });
    }

    /**
     * Next JE-{Ymd}-{seq}. Unique even with soft-deleted journal rows (unique index still applies).
     * Uses a DB sequence row + lockForUpdate() so concurrency and CACHE_DRIVER=array both work.
     */
    public static function generateNextEntryNumber(): string
    {
        $date = date('Ymd');
        $prefix = 'JE-' . $date . '-';

        return DB::transaction(function () use ($date, $prefix) {
            if (static::$journalEntrySequencesTableExists === null) {
                static::$journalEntrySequencesTableExists = Schema::hasTable('journal_entry_sequences');
            }
            if (! static::$journalEntrySequencesTableExists) {
                return static::generateNextEntryNumberLegacy($prefix);
            }

            DB::table('journal_entry_sequences')->insertOrIgnore([
                'day' => $date,
                'last_seq' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $seqRow = DB::table('journal_entry_sequences')
                ->where('day', $date)
                ->lockForUpdate()
                ->first();

            $maxFromJournal = 0;
            $maxRow = static::withTrashed()
                ->where('entry_number', 'like', $prefix . '%')
                ->orderByDesc('entry_number')
                ->first();
            if ($maxRow && str_starts_with($maxRow->entry_number, $prefix)) {
                $suffix = substr($maxRow->entry_number, strlen($prefix));
                if (is_numeric($suffix)) {
                    $maxFromJournal = (int) $suffix;
                }
            }

            $next = max((int) ($seqRow->last_seq ?? 0), $maxFromJournal) + 1;

            DB::table('journal_entry_sequences')
                ->where('day', $date)
                ->update([
                    'last_seq' => $next,
                    'updated_at' => now(),
                ]);

            return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Fallback if migration not run yet (avoids boot failure).
     */
    protected static function generateNextEntryNumberLegacy(string $prefix): string
    {
        $maxRow = static::withTrashed()
            ->where('entry_number', 'like', $prefix . '%')
            ->orderByDesc('entry_number')
            ->first();

        $next = 1;
        if ($maxRow && str_starts_with($maxRow->entry_number, $prefix)) {
            $suffix = substr($maxRow->entry_number, strlen($prefix));
            if (is_numeric($suffix)) {
                $next = (int) $suffix + 1;
            }
        }

        $candidate = $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
        while (static::withTrashed()->where('entry_number', $candidate)->exists()) {
            $next++;
            $candidate = $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
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

    /** Relation names for show/detail (connected data). */
    public static function getStandardRelations(): array
    {
        return ['items.account', 'creator', 'poster'];
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

    /**
     * Get URL to the source document (invoice, PO, service, payment, expense, return) when this entry was auto-created.
     * Returns null for manual entries or unknown reference types.
     */
    public function getSourceUrl(): ?string
    {
        if (empty($this->reference_type) || empty($this->reference_id)) {
            return null;
        }
        $type = $this->reference_type;
        $id = $this->reference_id;
        $routes = [
            'sale' => ['route' => 'sales.show', 'param' => 'sale'],
            'purchase' => ['route' => 'purchases.show', 'param' => 'purchase'],
            'payment' => ['route' => 'payments.show', 'param' => 'payment'],
            'service' => ['route' => 'services.show', 'param' => 'service'],
            'sale_return' => ['route' => 'sale-returns.show', 'param' => 'sale_return'],
            'purchase_return' => ['route' => 'purchase-returns.show', 'param' => 'purchase_return'],
            'service_return' => ['route' => 'service-returns.show', 'param' => 'service_return'],
            \App\Models\Expense::class => ['route' => 'expenses.show', 'param' => 'expense'],
        ];
        if (!isset($routes[$type])) {
            return null;
        }
        try {
            return route($routes[$type]['route'], [$routes[$type]['param'] => $id]);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Human-readable label for the source document (e.g. "Invoice INV-001").
     */
    public function getSourceLabel(): ?string
    {
        if (empty($this->reference_type) || empty($this->reference_id)) {
            return null;
        }
        $labels = [
            'sale' => 'Invoice',
            'purchase' => 'Purchase Order',
            'payment' => 'Payment',
            'service' => 'Service',
            'sale_return' => 'Sale Return',
            'purchase_return' => 'Purchase Return',
            'service_return' => 'Service Return',
            \App\Models\Expense::class => 'Expense',
        ];
        $label = $labels[$this->reference_type] ?? null;
        if (!$label) {
            return null;
        }
        return $label . ($this->reference ? ' ' . $this->reference : ' #' . $this->reference_id);
    }
}
