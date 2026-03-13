@extends('layouts.dashboard')

@section('title', 'Chart of Accounts')
@section('page-title', 'Chart of Accounts')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-book me-2"></i>Chart of Accounts</h6>
        @can('create accounts')
        <a href="{{ route('accounts.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>New Account
        </a>
        @endcan
    </div>

    <div class="filter-wrapper">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search account code or name...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="type" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="asset"     {{ request('type')=='asset'     ?'selected':'' }}>Assets</option>
                        <option value="liability" {{ request('type')=='liability' ?'selected':'' }}>Liabilities</option>
                        <option value="equity"    {{ request('type')=='equity'    ?'selected':'' }}>Equity</option>
                        <option value="revenue"   {{ request('type')=='revenue'   ?'selected':'' }}>Revenue</option>
                        <option value="expense"   {{ request('type')=='expense'   ?'selected':'' }}>Expenses</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary flex-grow-1">Search</button>
                    @if(request()->anyFilled(['search','type']))
                    <a href="{{ route('accounts.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="p-4">
        @foreach(['asset' => ['label'=>'Assets','color'=>'#16a34a','icon'=>'fa-building'],
                  'liability' => ['label'=>'Liabilities','color'=>'#ef4444','icon'=>'fa-credit-card'],
                  'equity' => ['label'=>'Equity','color'=>'#8b5cf6','icon'=>'fa-chart-pie'],
                  'revenue' => ['label'=>'Revenue','color'=>'#3b82f6','icon'=>'fa-arrow-trend-up'],
                  'expense' => ['label'=>'Expenses','color'=>'#f97316','icon'=>'fa-receipt']] as $type => $meta)
            @if($accounts->has($type))
            <div class="mb-5">
                {{-- Section header --}}
                <div class="d-flex align-items-center gap-2 mb-3 pb-2" style="border-bottom:2px solid {{ $meta['color'] }}20;">
                    <div style="width:28px;height:28px;background:{{ $meta['color'] }}15;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas {{ $meta['icon'] }}" style="color:{{ $meta['color'] }};font-size:12px;"></i>
                    </div>
                    <span style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:{{ $meta['color'] }};">
                        {{ $meta['label'] }}
                    </span>
                    <span class="badge" style="background:{{ $meta['color'] }}15;color:{{ $meta['color'] }};font-weight:700;">
                        {{ $accounts[$type]->count() }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:12%;">Code</th>
                                <th>Account Name</th>
                                <th style="width:18%;">Category</th>
                                <th style="width:12%;" class="text-center">Balance Type</th>
                                <th style="width:18%;" class="text-end">Current Balance</th>
                                <th style="width:10%;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts[$type] as $account)
                            <tr>
                                <td>
                                    <code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:12px;">{{ $account->code }}</code>
                                </td>
                                <td>
                                    <span style="font-size:14px;">{{ $account->name }}</span>
                                    @if($account->is_system)
                                        <span class="badge" style="background:#eff6ff;color:#1d4ed8;font-size:10px;margin-left:4px;">System</span>
                                    @endif
                                    @if(!$account->is_active)
                                        <span class="badge bg-secondary" style="font-size:10px;margin-left:4px;">Inactive</span>
                                    @endif
                                </td>
                                <td style="font-size:12px;color:#6b7280;">{{ ucfirst(str_replace('_',' ',$account->category)) }}</td>
                                <td class="text-center">
                                    <span class="badge" style="background:{{ $account->balance_type==='debit' ? '#fef2f2' : '#f0fdf4' }};color:{{ $account->balance_type==='debit' ? '#991b1b' : '#166534' }};font-size:11px;">
                                        {{ ucfirst($account->balance_type) }}
                                    </span>
                                </td>
                                <td class="text-end fw-semibold" style="color:{{ $account->current_balance >= 0 ? '#16a34a' : '#ef4444' }};">
                                    ৳{{ number_format(abs($account->current_balance), 2) }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('accounts.show', $account) }}" class="btn btn-outline-primary" title="View Ledger"><i class="fas fa-eye"></i></a>
                                        @if(!$account->is_system)
                                        <a href="{{ route('accounts.edit', $account) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>
@endsection
