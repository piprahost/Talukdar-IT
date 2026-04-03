<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Account;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view bank-accounts');
        $query = BankAccount::with('account')->latest();

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('account_name', 'like', "%{$request->search}%")
                  ->orWhere('bank_name', 'like', "%{$request->search}%")
                  ->orWhere('account_number', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $bankAccounts = $query->paginate(15)->appends($request->query());
        
        // Calculate total balance
        $totalBalance = BankAccount::active()->sum('current_balance');
        
        return view('accounting.bank-accounts.index', compact('bankAccounts', 'totalBalance'));
    }

    public function create()
    {
        $this->authorizePermission('create bank-accounts');
        // Get bank account type accounts from chart of accounts
        $accounts = Account::where('type', 'asset')
            ->where('category', 'current_asset')
            ->where('code', 'like', '1100%')
            ->active()
            ->orderBy('code')
            ->get();
        
        return view('accounting.bank-accounts.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create bank-accounts');
        $validated = $request->validate([
            'account_name' => ['required', 'string', 'max:255'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:100', 'unique:bank_accounts,account_number'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'routing_number' => ['nullable', 'string', 'max:50'],
            'swift_code' => ['nullable', 'string', 'max:50'],
            'account_type' => ['required', 'in:checking,savings,fixed_deposit'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $bankAccount = BankAccount::create($validated);
        
        // Set current balance equal to opening balance initially
        $bankAccount->current_balance = $bankAccount->opening_balance;
        $bankAccount->save();

        // Create or link to chart of accounts
        if (!$bankAccount->account_id) {
            $account = Account::firstOrCreate(
                [
                    'code' => '1100-' . str_pad($bankAccount->id, 3, '0', STR_PAD_LEFT),
                    'type' => 'asset',
                    'category' => 'current_asset',
                ],
                [
                    'name' => $bankAccount->bank_name . ' - ' . $bankAccount->account_name,
                    'balance_type' => 'debit',
                    'opening_balance' => $bankAccount->opening_balance,
                    'is_system' => false,
                    'is_active' => true,
                ]
            );
            $bankAccount->account_id = $account->id;
            $bankAccount->save();
        }

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Bank account created successfully.');
    }

    public function show(BankAccount $bankAccount)
    {
        $this->authorizePermission('view bank-accounts');
        $bankAccount->load('account');
        return view('accounting.bank-accounts.show', compact('bankAccount'));
    }

    public function edit(BankAccount $bankAccount)
    {
        $this->authorizePermission('edit bank-accounts');
        $accounts = Account::where('type', 'asset')
            ->where('category', 'current_asset')
            ->active()
            ->orderBy('code')
            ->get();
        
        return view('accounting.bank-accounts.edit', compact('bankAccount', 'accounts'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $this->authorizePermission('edit bank-accounts');
        $validated = $request->validate([
            'account_name' => ['required', 'string', 'max:255'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:100', 'unique:bank_accounts,account_number,' . $bankAccount->id],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'routing_number' => ['nullable', 'string', 'max:50'],
            'swift_code' => ['nullable', 'string', 'max:50'],
            'account_type' => ['required', 'in:checking,savings,fixed_deposit'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $bankAccount->update($validated);
        
        // Update linked account if exists
        if ($bankAccount->account) {
            $bankAccount->account->update([
                'name' => $bankAccount->bank_name . ' - ' . $bankAccount->account_name,
                'opening_balance' => $bankAccount->opening_balance,
            ]);
        }

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Bank account updated successfully.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $this->authorizePermission('delete bank-accounts');
        $bankAccount->forceDelete();

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Bank account deleted successfully.');
    }

    public function updateBalance(BankAccount $bankAccount)
    {
        $this->authorizePermission('update bank-balances');
        $bankAccount->updateBalance();
        
        return back()->with('success', 'Balance updated successfully.');
    }
}
