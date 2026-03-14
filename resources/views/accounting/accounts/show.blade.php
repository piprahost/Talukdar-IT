@extends('layouts.dashboard')

@section('title', 'Account Details')
@section('page-title', 'Account Details')

@section('content')
<div class="row g-3">
    <div class="col-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-book me-2"></i>{{ $account->code }} - {{ $account->name }}</h6>
                <div>
                    @if(!$account->is_system)
                        <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-outline-warning me-2">Edit</a>
                    @endif
                    <a href="{{ route('accounts.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>

            {{-- Balance as module-stat-card style --}}
            <div class="row g-2 p-4 pb-0">
                <div class="col-md-4">
                    <div class="module-stat-card rounded-3 p-3 h-100" style="background:{{ $account->current_balance >= 0 ? 'linear-gradient(135deg,#f0fdf4,#dcfce7)' : 'linear-gradient(135deg,#fef2f2,#fee2e2)' }};border-left:4px solid {{ $account->current_balance >= 0 ? '#16a34a' : '#ef4444' }};">
                        <div class="small text-muted text-uppercase fw-bold" style="font-size:11px;letter-spacing:.5px;">Current Balance</div>
                        <div class="fw-bold" style="font-size:24px;color:{{ $account->current_balance >= 0 ? '#166534' : '#991b1b' }};">
                            ৳{{ number_format(abs($account->current_balance), 2) }}
                            @if($account->current_balance < 0)<span class="small">(Cr)</span>@endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="module-stat-card rounded-3 p-3 h-100" style="background:#f8fafc;border-left:4px solid #64748b;">
                        <div class="small text-muted text-uppercase fw-bold" style="font-size:11px;letter-spacing:.5px;">Opening Balance</div>
                        <div class="fw-bold" style="font-size:20px;color:#334155;">৳{{ number_format($account->opening_balance, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="module-stat-card rounded-3 p-3 h-100" style="background:#f8fafc;border-left:4px solid #64748b;">
                        <div class="small text-muted text-uppercase fw-bold" style="font-size:11px;letter-spacing:.5px;">Type</div>
                        <div class="fw-bold" style="font-size:16px;color:#334155;">{{ ucfirst($account->type) }} · {{ ucfirst(str_replace('_', ' ', $account->category)) }}</div>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Account Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr><th width="40%" class="text-muted fw-normal">Account Code</th><td><code>{{ $account->code }}</code></td></tr>
                            <tr><th class="text-muted fw-normal">Account Name</th><td><strong>{{ $account->name }}</strong></td></tr>
                            <tr><th class="text-muted fw-normal">Type</th><td><span class="badge bg-secondary">{{ ucfirst($account->type) }}</span></td></tr>
                            <tr><th class="text-muted fw-normal">Category</th><td>{{ ucfirst(str_replace('_', ' ', $account->category)) }}</td></tr>
                            <tr><th class="text-muted fw-normal">Balance Type</th><td><span class="badge bg-primary">{{ ucfirst($account->balance_type) }}</span></td></tr>
                            @if($account->parent)
                            <tr><th class="text-muted fw-normal">Parent Account</th><td><a href="{{ route('accounts.show', $account->parent) }}">{{ $account->parent->code }} - {{ $account->parent->name }}</a></td></tr>
                            @endif
                            <tr><th class="text-muted fw-normal">Status</th><td>
                                <span class="badge {{ $account->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $account->is_active ? 'Active' : 'Inactive' }}</span>
                                @if($account->is_system)<span class="badge bg-info ms-1">System</span>@endif
                            </td></tr>
                        </table>
                    </div>
                    @if($account->description)
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Description</h6>
                        <p class="bg-light p-3 rounded mb-0">{{ $account->description }}</p>
                    </div>
                    @endif
                </div>

                @if($account->children->count() > 0)
                <div class="mt-4">
                    <h6 class="border-bottom pb-2 mb-3">Sub-Accounts</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead><tr><th>Code</th><th>Name</th><th>Balance</th></tr></thead>
                            <tbody>
                                @foreach($account->children as $child)
                                <tr>
                                    <td><code>{{ $child->code }}</code></td>
                                    <td><a href="{{ route('accounts.show', $child) }}">{{ $child->name }}</a></td>
                                    <td class="{{ $child->current_balance >= 0 ? 'text-success' : 'text-danger' }}">৳{{ number_format(abs($child->current_balance), 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Recent Journal Entries --}}
                @php $recentItems = $account->journalEntryItems->sortByDesc(fn($i) => $i->journalEntry?->entry_date)->take(15); @endphp
                @if($recentItems->count() > 0)
                <div class="mt-4">
                    <h6 class="border-bottom pb-2 mb-3">Recent Journal Entries</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Description</th>
                                    <th class="text-end">Debit</th>
                                    <th class="text-end">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentItems as $item)
                                    @php $je = $item->journalEntry; @endphp
                                    <tr>
                                        <td>{{ $je ? $je->entry_date->format('d M Y') : '—' }}</td>
                                        <td>
                                            @if($je)
                                                <a href="{{ route('journal-entries.show', $je) }}">{{ $je->entry_number ?? 'JE-' . $je->id }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $je->description ?? '—' }}</td>
                                        <td class="text-end {{ $item->debit > 0 ? 'text-success fw-semibold' : 'text-muted' }}">{{ $item->debit > 0 ? '৳' . number_format($item->debit, 2) : '—' }}</td>
                                        <td class="text-end {{ $item->credit > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">{{ $item->credit > 0 ? '৳' . number_format($item->credit, 2) : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">Showing latest {{ $recentItems->count() }} entries. <a href="{{ route('accounting.ledger') }}?account_id={{ $account->id }}">View full ledger</a></small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
