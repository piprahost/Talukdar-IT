@extends('layouts.dashboard')

@section('title', 'Chart of Accounts')
@section('page-title', 'Chart of Accounts')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-book me-2"></i>Chart of Accounts</h6>
        <a href="{{ route('accounts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Account
        </a>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-2">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search code or name...">
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-select" name="type" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="asset" {{ request('type')=='asset'?'selected':'' }}>Assets</option>
                    <option value="liability" {{ request('type')=='liability'?'selected':'' }}>Liabilities</option>
                    <option value="equity" {{ request('type')=='equity'?'selected':'' }}>Equity</option>
                    <option value="revenue" {{ request('type')=='revenue'?'selected':'' }}>Revenue</option>
                    <option value="expense" {{ request('type')=='expense'?'selected':'' }}>Expenses</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-outline-primary w-100">Search</button>
            </div>
        </div>
    </form>
    
    @foreach(['asset', 'liability', 'equity', 'revenue', 'expense'] as $type)
        @if($accounts->has($type))
        <div class="mb-4">
            <h6 class="text-uppercase mb-3">
                {{ ucfirst($type) }}s
                <span class="badge bg-secondary">{{ $accounts[$type]->count() }}</span>
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Code</th>
                            <th style="width: 35%;">Account Name</th>
                            <th style="width: 15%;">Type</th>
                            <th style="width: 10%;">Balance Type</th>
                            <th style="width: 15%;" class="text-end">Current Balance</th>
                            <th style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts[$type] as $account)
                            <tr>
                                <td><code>{{ $account->code }}</code></td>
                                <td>
                                    {{ $account->name }}
                                    @if($account->is_system)
                                        <span class="badge bg-info">System</span>
                                    @endif
                                    @if(!$account->is_active)
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary">{{ ucfirst($account->category) }}</span></td>
                                <td><span class="badge bg-primary">{{ ucfirst($account->balance_type) }}</span></td>
                                <td class="text-end">
                                    <strong class="{{ $account->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        ৳{{ number_format(abs($account->current_balance), 2) }}
                                    </strong>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('accounts.show', $account) }}" class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!$account->is_system)
                                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
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
@endsection

