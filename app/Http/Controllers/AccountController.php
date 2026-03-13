<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view accounts');
        $query = Account::with('parent')->orderBy('code');

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', "%{$request->search}%")
                  ->orWhere('name', 'like', "%{$request->search}%");
            });
        }

        $accounts = $query->get()->groupBy('type');
        
        return view('accounting.accounts.index', compact('accounts'));
    }

    public function create()
    {
        $this->authorizePermission('create accounts');
        $parentAccounts = Account::whereNull('parent_id')->orderBy('code')->get();
        return view('accounting.accounts.create', compact('parentAccounts'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create accounts');
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'category' => ['required', 'string'],
            'parent_id' => ['nullable', 'exists:accounts,id'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'balance_type' => ['required', 'in:debit,credit'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Account created successfully.');
    }

    public function show(Account $account)
    {
        $this->authorizePermission('view accounts');
        $account->load(['parent', 'children', 'journalEntryItems.journalEntry']);
        return view('accounting.accounts.show', compact('account'));
    }

    public function edit(Account $account)
    {
        $this->authorizePermission('edit accounts');
        if ($account->is_system) {
            return redirect()->route('accounts.index')
                ->with('error', 'System accounts cannot be edited.');
        }

        $parentAccounts = Account::whereNull('parent_id')
            ->where('id', '!=', $account->id)
            ->orderBy('code')
            ->get();

        return view('accounting.accounts.edit', compact('account', 'parentAccounts'));
    }

    public function update(Request $request, Account $account)
    {
        $this->authorizePermission('edit accounts');
        if ($account->is_system) {
            return redirect()->route('accounts.index')
                ->with('error', 'System accounts cannot be edited.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:accounts,code,' . $account->id],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'category' => ['required', 'string'],
            'parent_id' => ['nullable', 'exists:accounts,id'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'balance_type' => ['required', 'in:debit,credit'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        $this->authorizePermission('delete accounts');
        if ($account->is_system) {
            return redirect()->route('accounts.index')
                ->with('error', 'System accounts cannot be deleted.');
        }

        if ($account->journalEntryItems()->exists()) {
            return redirect()->route('accounts.index')
                ->with('error', 'Cannot delete account with journal entries.');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully.');
    }
}
