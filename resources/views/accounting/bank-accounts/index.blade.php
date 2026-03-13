@extends('layouts.dashboard')

@section('title', 'Bank Accounts')
@section('page-title', 'Bank Accounts')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="stat-card info">
            <div class="stat-icon">
                <i class="fas fa-university"></i>
            </div>
            <div class="stat-content">
                <h3>৳{{ number_format($totalBalance, 2) }}</h3>
                <p>Total Bank Balance</p>
            </div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-university me-2"></i>All Bank Accounts</h6>
        <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Bank Account
        </a>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-2">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search bank name, account number...">
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-select" name="is_active" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active')==='1'?'selected':'' }}>Active</option>
                    <option value="0" {{ request('is_active')==='0'?'selected':'' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-outline-primary w-100">Search</button>
            </div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Bank Name</th>
                    <th>Account Name</th>
                    <th>Account Number</th>
                    <th>Branch</th>
                    <th>Type</th>
                    <th>Current Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bankAccounts as $bankAccount)
                    <tr>
                        <td><strong>{{ $bankAccount->bank_name }}</strong></td>
                        <td>{{ $bankAccount->account_name }}</td>
                        <td><code>{{ $bankAccount->account_number }}</code></td>
                        <td>{{ $bankAccount->branch_name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $bankAccount->account_type)) }}</span>
                        </td>
                        <td>
                            <strong class="{{ $bankAccount->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                ৳{{ number_format($bankAccount->current_balance, 2) }}
                            </strong>
                        </td>
                        <td>
                            <span class="badge {{ $bankAccount->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $bankAccount->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('bank-accounts.show', $bankAccount) }}" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('bank-accounts.edit', $bankAccount) }}" class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No bank accounts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $bankAccounts->links() }}
</div>
@endsection

