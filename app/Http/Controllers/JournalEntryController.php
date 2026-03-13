<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view journal-entries');
        $query = JournalEntry::with(['creator', 'poster'])->latest();

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('entry_number', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('reference', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }

        $entries = $query->paginate(15)->appends($request->query());
        
        return view('accounting.journal-entries.index', compact('entries'));
    }

    public function create()
    {
        $this->authorizePermission('create journal-entries');
        $accounts = Account::active()->orderBy('code')->get();
        return view('accounting.journal-entries.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create journal-entries');
        $validated = $request->validate([
            'entry_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'reference' => ['nullable', 'string'],
            'reference_type' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:2'],
            'items.*.account_id' => ['required', 'exists:accounts,id'],
            'items.*.debit' => ['required_without:items.*.credit', 'numeric', 'min:0'],
            'items.*.credit' => ['required_without:items.*.debit', 'numeric', 'min:0'],
            'items.*.description' => ['nullable', 'string'],
        ]);

        // Validate that debit and credit totals match
        $totalDebit = collect($validated['items'])->sum('debit');
        $totalCredit = collect($validated['items'])->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors(['items' => 'Total debit and credit must be equal.'])->withInput();
        }

        $entry = DB::transaction(function () use ($validated) {
            $entry = JournalEntry::create([
                'entry_date' => $validated['entry_date'],
                'description' => $validated['description'],
                'reference' => $validated['reference'] ?? null,
                'reference_type' => $validated['reference_type'] ?? 'manual',
                'status' => 'draft',
            ]);

            foreach ($validated['items'] as $itemData) {
                $entry->items()->create([
                    'account_id' => $itemData['account_id'],
                    'debit' => $itemData['debit'] ?? 0,
                    'credit' => $itemData['credit'] ?? 0,
                    'description' => $itemData['description'] ?? null,
                ]);
            }

            return $entry;
        });

        return redirect()->route('journal-entries.show', $entry)
            ->with('success', 'Journal entry created successfully.');
    }

    public function show(JournalEntry $journalEntry)
    {
        $this->authorizePermission('view journal-entries');
        $journalEntry->load(['items.account', 'creator', 'poster']);
        return view('accounting.journal-entries.show', compact('journalEntry'));
    }

    public function edit(JournalEntry $journalEntry)
    {
        $this->authorizePermission('edit journal-entries');
        if ($journalEntry->status === 'posted') {
            return redirect()->route('journal-entries.show', $journalEntry)
                ->with('error', 'Posted journal entries cannot be edited.');
        }

        $accounts = Account::active()->orderBy('code')->get();
        $journalEntry->load('items.account');
        return view('accounting.journal-entries.edit', compact('journalEntry', 'accounts'));
    }

    public function update(Request $request, JournalEntry $journalEntry)
    {
        $this->authorizePermission('edit journal-entries');
        if ($journalEntry->status === 'posted') {
            return redirect()->route('journal-entries.show', $journalEntry)
                ->with('error', 'Posted journal entries cannot be edited.');
        }

        $validated = $request->validate([
            'entry_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'reference' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:2'],
            'items.*.account_id' => ['required', 'exists:accounts,id'],
            'items.*.debit' => ['required_without:items.*.credit', 'numeric', 'min:0'],
            'items.*.credit' => ['required_without:items.*.debit', 'numeric', 'min:0'],
            'items.*.description' => ['nullable', 'string'],
        ]);

        $totalDebit = collect($validated['items'])->sum('debit');
        $totalCredit = collect($validated['items'])->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors(['items' => 'Total debit and credit must be equal.'])->withInput();
        }

        DB::transaction(function () use ($journalEntry, $validated) {
            $journalEntry->update([
                'entry_date' => $validated['entry_date'],
                'description' => $validated['description'],
                'reference' => $validated['reference'] ?? null,
            ]);

            $journalEntry->items()->delete();

            foreach ($validated['items'] as $itemData) {
                $journalEntry->items()->create([
                    'account_id' => $itemData['account_id'],
                    'debit' => $itemData['debit'] ?? 0,
                    'credit' => $itemData['credit'] ?? 0,
                    'description' => $itemData['description'] ?? null,
                ]);
            }
        });

        return redirect()->route('journal-entries.show', $journalEntry)
            ->with('success', 'Journal entry updated successfully.');
    }

    public function destroy(JournalEntry $journalEntry)
    {
        $this->authorizePermission('delete journal-entries');
        if ($journalEntry->status === 'posted') {
            return redirect()->route('journal-entries.index')
                ->with('error', 'Posted journal entries cannot be deleted.');
        }

        $journalEntry->delete();

        return redirect()->route('journal-entries.index')
            ->with('success', 'Journal entry deleted successfully.');
    }

    public function post(JournalEntry $journalEntry)
    {
        $this->authorizePermission('post journal-entries');
        if ($journalEntry->status === 'posted') {
            return back()->with('error', 'Journal entry is already posted.');
        }

        if (!$journalEntry->isBalanced()) {
            return back()->with('error', 'Journal entry is not balanced. Cannot post.');
        }

        $journalEntry->post();

        return back()->with('success', 'Journal entry posted successfully.');
    }

    public function unpost(JournalEntry $journalEntry)
    {
        $this->authorizePermission('unpost journal-entries');
        if ($journalEntry->status !== 'posted') {
            return back()->with('error', 'Only posted entries can be unposted.');
        }

        $journalEntry->unpost();

        return back()->with('success', 'Journal entry unposted successfully.');
    }
}
