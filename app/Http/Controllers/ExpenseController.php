<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Account;
use App\Models\BankAccount;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view expenses');
        $query = Expense::withStandardRelations()->latest();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $expenses = $query->paginate(20)->appends($request->query());

        // Get statistics
        $stats = [
            'total' => Expense::count(),
            'total_amount' => Expense::sum('amount'),
            'draft' => Expense::where('status', 'draft')->count(),
            'approved' => Expense::where('status', 'approved')->count(),
            'paid' => Expense::where('status', 'paid')->count(),
            'this_month' => Expense::whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->sum('amount'),
        ];

        // Get unique categories for filter
        $categories = Expense::distinct()->pluck('category')->filter()->sort();

        return view('expenses.index', compact('expenses', 'stats', 'categories'));
    }

    public function create()
    {
        $this->authorizePermission('create expenses');
        // Get expense accounts from Chart of Accounts
        $expenseAccounts = Account::active()
            ->where('type', 'expense')
            ->orderBy('code')
            ->get();

        // Get active bank accounts
        $bankAccounts = BankAccount::active()->orderBy('account_name')->get();

        return view('expenses.create', compact('expenseAccounts', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create expenses');
        $validated = $request->validate([
            'expense_date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:255'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'vendor_contact' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'status' => ['required', 'in:draft,approved,paid,cancelled'],
            'payment_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            // Handle file upload
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('expenses', $filename, 'public');
                $validated['attachment'] = $path;
            }

            // Set payment_date if status is paid and payment_date is not provided
            if ($validated['status'] === 'paid' && empty($validated['payment_date'])) {
                $validated['payment_date'] = $validated['expense_date'];
            }

            // Set approved_by and approved_at if status is approved or paid
            if (in_array($validated['status'], ['approved', 'paid'])) {
                $validated['approved_by'] = auth()->id();
                $validated['approved_at'] = now();
            }

            $expense = Expense::create($validated);

            // Update bank account balance if paid via bank
            if ($expense->status === 'paid' && $expense->bank_account_id && $expense->bankAccount) {
                $expense->bankAccount->decrement('current_balance', $expense->amount);
            }

            // Accounting entry will be created via model observer if status is approved/paid
        });

        return redirect()->route('expenses.index')
            ->with('success', 'Expense created successfully.');
    }

    public function show(Expense $expense)
    {
        $this->authorizePermission('view expenses');
        $expense->load(Expense::getStandardRelations());
        
        // Load journal entry separately
        $journalEntry = \App\Models\JournalEntry::where('reference_type', Expense::class)
            ->where('reference_id', $expense->id)
            ->with('items.account')
            ->first();
        
        return view('expenses.show', compact('expense', 'journalEntry'));
    }

    public function edit(Expense $expense)
    {
        $this->authorizePermission('edit expenses');
        if ($expense->isPaid()) {
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'Paid expenses cannot be edited.');
        }

        $expenseAccounts = Account::active()
            ->where('type', 'expense')
            ->orderBy('code')
            ->get();

        $bankAccounts = BankAccount::active()->orderBy('account_name')->get();

        return view('expenses.edit', compact('expense', 'expenseAccounts', 'bankAccounts'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizePermission('edit expenses');
        if ($expense->isPaid()) {
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'Paid expenses cannot be updated.');
        }

        $validated = $request->validate([
            'expense_date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:255'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,card,mobile_banking,bank_transfer,cheque,other'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'vendor_contact' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'status' => ['required', 'in:draft,approved,paid,cancelled'],
            'payment_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request, $validated, $expense) {
            $oldStatus = $expense->status;
            $oldBankAccountId = $expense->bank_account_id;
            $oldAmount = $expense->amount;

            // Handle file upload
            if ($request->hasFile('attachment')) {
                // Delete old file if exists
                if ($expense->attachment && \Storage::disk('public')->exists($expense->attachment)) {
                    \Storage::disk('public')->delete($expense->attachment);
                }

                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('expenses', $filename, 'public');
                $validated['attachment'] = $path;
            }

            // Set payment_date if status is paid and payment_date is not provided
            if ($validated['status'] === 'paid' && empty($validated['payment_date'])) {
                $validated['payment_date'] = $validated['expense_date'];
            }

            // Set approved_by and approved_at if status changes to approved or paid
            if (in_array($validated['status'], ['approved', 'paid']) && !in_array($oldStatus, ['approved', 'paid'])) {
                $validated['approved_by'] = auth()->id();
                $validated['approved_at'] = now();
            }

            $expense->update($validated);

            // Handle bank account balance updates
            if ($expense->status === 'paid') {
                // Revert old bank account if changed
                if ($oldBankAccountId && $oldStatus === 'paid' && $oldBankAccountId != $expense->bank_account_id) {
                    $oldBankAccount = BankAccount::find($oldBankAccountId);
                    if ($oldBankAccount) {
                        $oldBankAccount->increment('current_balance', $oldAmount);
                    }
                }
                
                // Update new bank account
                if ($expense->bank_account_id && $expense->bankAccount) {
                    if ($oldStatus === 'paid' && $oldBankAccountId == $expense->bank_account_id) {
                        // Same bank account, adjust balance by difference
                        $difference = $expense->amount - $oldAmount;
                        if ($difference != 0) {
                            $expense->bankAccount->decrement('current_balance', abs($difference));
                        }
                    } else {
                        // New bank account or status changed to paid
                        $expense->bankAccount->decrement('current_balance', $expense->amount);
                    }
                }
            } else if ($oldStatus === 'paid' && $expense->status !== 'paid') {
                // Revert bank account if status changed from paid
                if ($oldBankAccountId) {
                    $oldBankAccount = BankAccount::find($oldBankAccountId);
                    if ($oldBankAccount) {
                        $oldBankAccount->increment('current_balance', $oldAmount);
                    }
                }
            }

            // Accounting entry will be updated via model observer if status changed
        });

        $expense->refresh();
        if (in_array($expense->status, ['approved', 'paid'], true) && !in_array($oldStatus, ['approved', 'paid'])) {
            \App\Services\SmsNotificationService::expenseApproved($expense);
        }

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorizePermission('delete expenses');
        if ($expense->isPaid()) {
            return redirect()->route('expenses.index')
                ->with('error', 'Paid expenses cannot be deleted.');
        }

        DB::transaction(function () use ($expense) {
            // Delete attachment if exists
            if ($expense->attachment && \Storage::disk('public')->exists($expense->attachment)) {
                \Storage::disk('public')->delete($expense->attachment);
            }

            // Delete related journal entry
            AccountingService::deleteJournalEntry(Expense::class, $expense->id);

            $expense->delete();
        });

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    public function approve(Expense $expense)
    {
        $this->authorizePermission('approve expenses');
        if ($expense->isPaid() || $expense->isCancelled()) {
            return back()->with('error', 'Cannot approve this expense.');
        }

        $expense->approve();

        \App\Services\SmsNotificationService::expenseApproved($expense->fresh());

        return back()->with('success', 'Expense approved successfully.');
    }

    public function markAsPaid(Request $request, Expense $expense)
    {
        $this->authorizePermission('mark-expenses-paid');
        if ($expense->isPaid()) {
            return back()->with('error', 'Expense is already paid.');
        }

        if ($expense->isCancelled()) {
            return back()->with('error', 'Cannot mark cancelled expense as paid.');
        }

        $validated = $request->validate([
            'payment_date' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($expense, $validated) {
            $expense->markAsPaid($validated['payment_date'] ?? now());

            // Update bank account balance if paid via bank
            if ($expense->bank_account_id && $expense->bankAccount) {
                $expense->bankAccount->decrement('current_balance', $expense->amount);
            }
        });

        return back()->with('success', 'Expense marked as paid successfully.');
    }

    public function cancel(Expense $expense)
    {
        if ($expense->isPaid()) {
            return back()->with('error', 'Paid expenses cannot be cancelled.');
        }

        $expense->cancel();

        return back()->with('success', 'Expense cancelled successfully.');
    }
}
