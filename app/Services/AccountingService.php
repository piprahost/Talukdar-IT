<?php

namespace App\Services;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\PurchaseReturn;
use App\Models\SaleReturn;
use App\Models\ServiceReturn;
use App\Models\Service;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Resolve the correct asset account (cash or bank) based on payment method and bank_account_id.
     */
    protected static function cashOrBankAccount(?string $paymentMethod, ?int $bankAccountId): ?Account
    {
        // For bank-related methods, prefer the linked bank account's COA account
        if (in_array($paymentMethod, ['bank_transfer', 'mobile_banking', 'card', 'cheque'], true) && $bankAccountId) {
            $bank = BankAccount::find($bankAccountId);
            if ($bank && $bank->account) {
                return $bank->account;
            }
        }

        // Fallback to main cash account (code 1000)
        return Account::where('code', '1000')->first();
    }

    /**
     * After posting to an asset account, sync the linked bank account balance (if any).
     */
    protected static function syncBankBalanceForAccount(?Account $asset): void
    {
        if (!$asset) {
            return;
        }

        $bank = BankAccount::where('account_id', $asset->id)->first();
        if ($bank) {
            $bank->updateBalance();
        }
    }

    /**
     * Create journal entry for a sale
     */
    public static function recordSale(Sale $sale)
    {
        if ($sale->status !== 'completed') {
            return;
        }

        // Check if journal entry already exists - delete and recreate if exists
        JournalEntry::where('reference_type', 'sale')
            ->where('reference_id', $sale->id)
            ->delete();

        $accounts = [
            'accounts_receivable' => Account::where('code', '1200')->first(),
            'sales_revenue' => Account::where('code', '4000')->first(),
        ];

        DB::transaction(function () use ($sale, $accounts) {
            $assetAccount = self::cashOrBankAccount($sale->payment_method, $sale->bank_account_id);

            $entry = JournalEntry::create([
                'entry_date' => $sale->sale_date,
                'description' => "Sale - Invoice: {$sale->invoice_number}",
                'reference' => $sale->invoice_number,
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            // Accounts Receivable or Cash (if paid)
            if ($sale->paid_amount > 0 && $assetAccount) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $assetAccount->id,
                    'debit' => $sale->paid_amount,
                    'credit' => 0,
                    'description' => "Cash received for sale",
                ]);
            }

            if ($sale->due_amount > 0) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accounts['accounts_receivable']->id,
                    'debit' => $sale->due_amount,
                    'credit' => 0,
                    'description' => "Accounts receivable for sale",
                ]);
            }

            // Sales Revenue
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $accounts['sales_revenue']->id,
                'debit' => 0,
                'credit' => $sale->total_amount,
                'description' => "Sales revenue",
            ]);

            self::syncBankBalanceForAccount($assetAccount);
        });
    }

    /**
     * Create journal entry for a purchase
     */
    public static function recordPurchase(Purchase $purchase)
    {
        // Check if journal entry already exists - delete and recreate if exists
        JournalEntry::where('reference_type', 'purchase')
            ->where('reference_id', $purchase->id)
            ->delete();

        $accounts = [
            'inventory' => Account::where('code', '1300')->first(),
            'accounts_payable' => Account::where('code', '2000')->first(),
        ];

        DB::transaction(function () use ($purchase, $accounts) {
            $assetAccount = self::cashOrBankAccount($purchase->payment_method, $purchase->bank_account_id);

            $entry = JournalEntry::create([
                'entry_date' => $purchase->order_date,
                'description' => "Purchase - PO: {$purchase->po_number}",
                'reference' => $purchase->po_number,
                'reference_type' => 'purchase',
                'reference_id' => $purchase->id,
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            // Inventory (debit)
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $accounts['inventory']->id,
                'debit' => $purchase->total_amount,
                'credit' => 0,
                'description' => "Inventory purchase",
            ]);

            // Accounts Payable or Cash (if paid)
            if ($purchase->paid_amount > 0 && $assetAccount) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $assetAccount->id,
                    'debit' => 0,
                    'credit' => $purchase->paid_amount,
                    'description' => "Cash paid for purchase",
                ]);
            }

            if ($purchase->due_amount > 0) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accounts['accounts_payable']->id,
                    'debit' => 0,
                    'credit' => $purchase->due_amount,
                    'description' => "Accounts payable for purchase",
                ]);
            }

            self::syncBankBalanceForAccount($assetAccount);
        });
    }

    /**
     * Create journal entry for a payment (customer or supplier)
     */
    public static function recordPayment(Payment $payment)
    {
        // Refund payments (negative amount) are created by sale/purchase return complete; accounting is in recordSaleReturn/recordPurchaseReturn
        if ((float) $payment->amount < 0) {
            return;
        }
        // Service payments are reflected in the service journal entry (recordService), not a separate payment entry
        if ($payment->payment_type === 'customer' && $payment->service_id) {
            return;
        }

        JournalEntry::where('reference_type', 'payment')
            ->where('reference_id', $payment->id)
            ->delete();

        $accounts = [
            'accounts_receivable' => Account::where('code', '1200')->first(),
            'accounts_payable'    => Account::where('code', '2000')->first(),
        ];

        DB::transaction(function () use ($payment, $accounts) {
            $assetAccount = self::cashOrBankAccount(
                $payment->payment_method,
                $payment->bank_account_id
            );

            if ($payment->payment_type === 'customer') {
                $sale = $payment->sale;
                $entry = JournalEntry::create([
                    'entry_date' => $payment->payment_date,
                    'description' => "Customer Payment - Invoice: {$sale->invoice_number}",
                    'reference' => $payment->payment_number,
                    'reference_type' => 'payment',
                    'reference_id' => $payment->id,
                    'status' => 'posted',
                    'posted_by' => auth()->id(),
                    'posted_at' => now(),
                ]);

                // Cash/Bank (debit)
                if ($assetAccount) {
                    JournalEntryItem::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $assetAccount->id,
                        'debit' => $payment->amount,
                        'credit' => 0,
                        'description' => "Payment received from customer",
                    ]);
                }

                // Accounts Receivable (credit)
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accounts['accounts_receivable']->id,
                    'debit' => 0,
                    'credit' => $payment->amount,
                    'description' => "Reduce accounts receivable",
                ]);
            } else {
                // Supplier payment
                $purchase = $payment->purchase;
                $entry = JournalEntry::create([
                    'entry_date' => $payment->payment_date,
                    'description' => "Supplier Payment - PO: {$purchase->po_number}",
                    'reference' => $payment->payment_number,
                    'reference_type' => 'payment',
                    'reference_id' => $payment->id,
                    'status' => 'posted',
                    'posted_by' => auth()->id(),
                    'posted_at' => now(),
                ]);

                // Accounts Payable (debit)
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accounts['accounts_payable']->id,
                    'debit' => $payment->amount,
                    'credit' => 0,
                    'description' => "Reduce accounts payable",
                ]);

                // Cash/Bank (credit)
                if ($assetAccount) {
                    JournalEntryItem::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $assetAccount->id,
                        'debit' => 0,
                        'credit' => $payment->amount,
                        'description' => "Payment made to supplier",
                    ]);
                }
            }

            self::syncBankBalanceForAccount($assetAccount);
        });
    }

    /**
     * Create journal entry for a purchase return
     */
    public static function recordPurchaseReturn(PurchaseReturn $return)
    {
        if ($return->status !== 'completed') {
            return;
        }

        JournalEntry::where('reference_type', 'purchase_return')
            ->where('reference_id', $return->id)
            ->delete();

        $accounts = [
            'inventory' => Account::where('code', '1300')->first(),
            'accounts_payable' => Account::where('code', '2000')->first(),
            'cash' => Account::where('code', '1000')->first(),
        ];

        DB::transaction(function () use ($return, $accounts) {
            $entry = JournalEntry::create([
                'entry_date' => $return->return_date,
                'description' => "Purchase Return - Return #: {$return->return_number}",
                'reference' => $return->return_number,
                'reference_type' => 'purchase_return',
                'reference_id' => $return->id,
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            // Reduce Inventory (credit)
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $accounts['inventory']->id,
                'debit' => 0,
                'credit' => $return->total_amount,
                'description' => "Inventory reduction for purchase return",
            ]);

            // Reduce Accounts Payable or increase Cash (debit)
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $accounts['accounts_payable']->id,
                'debit' => $return->total_amount,
                'credit' => 0,
                'description' => "Reduce accounts payable for return",
            ]);
        });
    }

    /**
     * Create journal entry for a sale return
     */
    public static function recordSaleReturn(SaleReturn $return)
    {
        if ($return->status !== 'completed') {
            return;
        }

        JournalEntry::where('reference_type', 'sale_return')
            ->where('reference_id', $return->id)
            ->delete();

        $accounts = [
            'sales_revenue' => Account::where('code', '4000')->first(),
            'accounts_receivable' => Account::where('code', '1200')->first(),
            'cash' => Account::where('code', '1000')->first(),
            'inventory' => Account::where('code', '1300')->first(),
        ];

        $return->load('items.product');
        
        // Calculate total cost (using product cost prices)
        $totalCost = $return->items->sum(function($item) {
            return ($item->product->cost_price ?? 0) * $item->quantity;
        });

        DB::transaction(function () use ($return, $accounts, $totalCost) {
            $entry = JournalEntry::create([
                'entry_date' => $return->return_date,
                'description' => "Sale Return - Return #: {$return->return_number}",
                'reference' => $return->return_number,
                'reference_type' => 'sale_return',
                'reference_id' => $return->id,
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            // Reduce Sales Revenue (debit)
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $accounts['sales_revenue']->id,
                'debit' => $return->total_amount,
                'credit' => 0,
                'description' => "Sales revenue reduction for return",
            ]);

            // Reduce Accounts Receivable (credit) - refund to customer
            // Note: If customer paid, they would get cash refund, but we credit AR to reverse original sale
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $accounts['accounts_receivable']->id,
                'debit' => 0,
                'credit' => $return->total_amount,
                'description' => "Refund to customer for return",
            ]);

            // Add Inventory back (debit) - using product cost prices
            if ($totalCost > 0) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accounts['inventory']->id,
                    'debit' => $totalCost,
                    'credit' => 0,
                    'description' => "Inventory returned at cost",
                ]);

                // Reduce Cost of Goods Sold (credit)
                $cogsAccount = Account::where('code', '5000')->first(); // COGS account
                if ($cogsAccount) {
                    JournalEntryItem::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $cogsAccount->id,
                        'debit' => 0,
                        'credit' => $totalCost,
                        'description' => "Reduce COGS for return",
                    ]);
                }
            }
        });
    }

    /**
     * Create journal entry for a completed/delivered service order (service revenue).
     * Call when service is created/updated/paid or status set to completed/delivered.
     * Removes the entry when status is not completed/delivered (e.g. cancelled).
     */
    public static function recordService(Service $service)
    {
        // Remove any existing journal entry for this service (idempotent; also clears on cancel)
        JournalEntry::where('reference_type', 'service')
            ->where('reference_id', $service->id)
            ->delete();

        $total = (float) $service->service_cost;
        if ($total <= 0) {
            return;
        }

        $status = $service->status;
        if (!in_array($status, ['completed', 'delivered'], true)) {
            return;
        }

        $accounts = [
            'accounts_receivable' => Account::where('code', '1200')->first(),
            'service_revenue'     => Account::where('code', '4100')->first(),
        ];

        if (!$accounts['service_revenue']) {
            return;
        }

        DB::transaction(function () use ($service, $accounts) {
            $assetAccount = self::cashOrBankAccount($service->payment_method, $service->bank_account_id);
            $entry = JournalEntry::create([
                'entry_date'   => $service->delivery_date ?? $service->receive_date ?? now(),
                'description'  => "Service - SN: {$service->service_number}",
                'reference'    => $service->service_number,
                'reference_type' => 'service',
                'reference_id' => $service->id,
                'status'       => 'posted',
                'posted_by'    => auth()->id(),
                'posted_at'    => now(),
            ]);

            $paid = (float) $service->paid_amount;
            $due  = (float) $service->due_amount;

            if ($paid > 0 && $assetAccount) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id'       => $assetAccount->id,
                    'debit'            => $paid,
                    'credit'           => 0,
                    'description'      => 'Cash received for service',
                ]);
            }

            if ($due > 0 && $accounts['accounts_receivable']) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id'       => $accounts['accounts_receivable']->id,
                    'debit'            => $due,
                    'credit'           => 0,
                    'description'      => 'Receivable for service',
                ]);
            }

            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $accounts['service_revenue']->id,
                'debit'            => 0,
                'credit'           => (float) $service->service_cost,
                'description'      => 'Service revenue',
            ]);

            self::syncBankBalanceForAccount($assetAccount);
        });
    }

    /**
     * Create journal entry for a service return
     */
    public static function recordServiceReturn(ServiceReturn $return)
    {
        if ($return->status !== 'completed' || $return->refund_amount <= 0) {
            return;
        }

        JournalEntry::where('reference_type', 'service_return')
            ->where('reference_id', $return->id)
            ->delete();

        $accounts = [
            'service_revenue' => Account::where('code', '4100')->first(),
            'cash' => Account::where('code', '1000')->first(),
        ];

        DB::transaction(function () use ($return, $accounts) {
            $entry = JournalEntry::create([
                'entry_date' => $return->return_date,
                'description' => "Service Return - Return #: {$return->return_number}",
                'reference' => $return->return_number,
                'reference_type' => 'service_return',
                'reference_id' => $return->id,
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            // Reduce Service Revenue (debit)
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $accounts['service_revenue']->id,
                'debit' => $return->refund_amount,
                'credit' => 0,
                'description' => "Service revenue reduction for return",
            ]);

            // Cash paid out (credit)
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $accounts['cash']->id,
                'debit' => 0,
                'credit' => $return->refund_amount,
                'description' => "Cash refund for service return",
            ]);
        });
    }

    /**
     * Record an expense transaction as a journal entry.
     */
    public static function recordExpense(Expense $expense)
    {
        if (!in_array($expense->status, ['approved', 'paid'])) {
            return;
        }

        DB::transaction(function () use ($expense) {
            // Delete existing entry if any (e.g., if expense was updated)
            self::deleteJournalEntry(Expense::class, $expense->id);

            // Get expense account (use linked account or default expense account)
            $expenseAccount = $expense->account;
            if (!$expenseAccount) {
                // Try to find a default expense account by category or use a general expense account
                $expenseAccount = Account::active()
                    ->where('type', 'expense')
                    ->where('code', '5000') // General Expense account code
                    ->first();

                if (!$expenseAccount) {
                    // Fallback: get any expense account
                    $expenseAccount = Account::active()
                        ->where('type', 'expense')
                        ->first();
                }

                if (!$expenseAccount) {
                    throw new \Exception("No expense account found. Please create expense accounts in Chart of Accounts.");
                }
            }

            // Get cash or bank account based on payment method
            $cashOrBankAccount = self::cashOrBankAccount($expense->payment_method, $expense->bank_account_id);
            if (!$cashOrBankAccount) {
                throw new \Exception("No cash/bank account found. Please create cash accounts in Chart of Accounts.");
            }

            $entry = JournalEntry::create([
                'entry_number' => 'JE-EXP-' . $expense->expense_number,
                'entry_date' => $expense->expense_date,
                'description' => 'Expense: ' . $expense->category . ' - ' . ($expense->description ?? $expense->expense_number),
                'status' => 'posted',
                'created_by' => $expense->created_by,
                'reference_type' => Expense::class,
                'reference_id' => $expense->id,
            ]);

            // Debit Expense Account, Credit Cash/Bank Account
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $expenseAccount->id,
                'debit' => $expense->amount,
                'credit' => 0,
                'description' => 'Expense: ' . $expense->category . ($expense->vendor_name ? ' - ' . $expense->vendor_name : ''),
            ]);

            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $cashOrBankAccount->id,
                'debit' => 0,
                'credit' => $expense->amount,
                'description' => 'Payment for expense: ' . $expense->expense_number,
            ]);

            self::syncBankBalanceForAccount($cashOrBankAccount);
        });
    }

    /**
     * Delete journal entry when transaction is deleted
     */
    public static function deleteJournalEntry($referenceType, $referenceId)
    {
        JournalEntry::where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->delete();
    }
}

