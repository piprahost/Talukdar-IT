@extends('layouts.dashboard')

@section('title', 'Bank Accounts')
@section('page-title', 'Bank Accounts')

@section('content')

{{-- Stats --}}
<div class="module-stats mb-3">
    <div class="module-stat-card" style="border-left:3px solid #16a34a;">
        <div class="msc-label">Total Bank Balance</div>
        <div class="msc-value" style="font-size:18px;color:#16a34a;">৳{{ number_format($totalBalance, 2) }}</div>
        <div class="msc-sub">Across all accounts</div>
    </div>
    <div class="module-stat-card">
        <div class="msc-label">Total Accounts</div>
        <div class="msc-value">{{ $bankAccounts->total() }}</div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-university me-2"></i>All Bank Accounts</h6>
        @can('create bank-accounts')
        <a href="{{ route('bank-accounts.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>New Bank Account
        </a>
        @endcan
    </div>

    <div class="filter-wrapper">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Bank name, account number...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="is_active" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active')==='1'?'selected':'' }}>Active</option>
                        <option value="0" {{ request('is_active')==='0'?'selected':'' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary flex-grow-1">Search</button>
                    @if(request()->anyFilled(['search','is_active']))
                    <a href="{{ route('bank-accounts.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Bank</th>
                    <th>Account Details</th>
                    <th>Branch</th>
                    <th class="text-center">Type</th>
                    <th class="text-end">Current Balance</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bankAccounts as $account)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-university" style="color:#3b82f6;font-size:16px;"></i>
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:14px;">{{ $account->bank_name }}</div>
                                <div style="font-size:11px;color:#9ca3af;">{{ $account->account_name }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:12px;">{{ $account->account_number }}</code>
                        @if($account->routing_number)
                        <div style="font-size:11px;color:#9ca3af;">Routing: {{ $account->routing_number }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $account->branch_name ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge" style="background:#f3f4f6;color:#374151;font-size:11px;">
                            {{ ucfirst(str_replace('_', ' ', $account->account_type)) }}
                        </span>
                    </td>
                    <td class="text-end fw-bold" style="font-size:15px;color:{{ $account->current_balance >= 0 ? '#16a34a' : '#ef4444' }};">
                        ৳{{ number_format($account->current_balance, 2) }}
                    </td>
                    <td class="text-center">
                        @if($account->is_active)
                            <span class="badge bg-success" style="font-size:11px;">Active</span>
                        @else
                            <span class="badge bg-secondary" style="font-size:11px;">Inactive</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('bank-accounts.show', $account) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('bank-accounts.edit', $account) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-university fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">No bank accounts found.</p>
                        @can('create bank-accounts')
                        <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Bank Account
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bankAccounts->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">Showing {{ $bankAccounts->firstItem() }}–{{ $bankAccounts->lastItem() }} of {{ $bankAccounts->total() }} accounts</small>
        {{ $bankAccounts->links() }}
    </div>
    @endif
</div>
@endsection
