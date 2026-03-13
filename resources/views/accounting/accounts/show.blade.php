@extends('layouts.dashboard')

@section('title', 'Account Details')
@section('page-title', 'Account Details')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-book me-2"></i>{{ $account->code }} - {{ $account->name }}</h6>
                <div>
                    @if(!$account->is_system)
                        <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                    @endif
                    <a href="{{ route('accounts.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <div class="p-4">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h5 class="mb-0">Current Balance: 
                                <span class="{{ $account->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                    ৳{{ number_format(abs($account->current_balance), 2) }}
                                </span>
                            </h5>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Account Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Account Code:</th><td><code>{{ $account->code }}</code></td></tr>
                            <tr><th>Account Name:</th><td><strong>{{ $account->name }}</strong></td></tr>
                            <tr><th>Type:</th><td><span class="badge bg-secondary">{{ ucfirst($account->type) }}</span></td></tr>
                            <tr><th>Category:</th><td>{{ ucfirst(str_replace('_', ' ', $account->category)) }}</td></tr>
                            <tr><th>Balance Type:</th><td><span class="badge bg-primary">{{ ucfirst($account->balance_type) }}</span></td></tr>
                            @if($account->parent)
                            <tr><th>Parent Account:</th><td>{{ $account->parent->code }} - {{ $account->parent->name }}</td></tr>
                            @endif
                            <tr><th>Status:</th><td>
                                <span class="badge {{ $account->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $account->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if($account->is_system)
                                    <span class="badge bg-info ms-1">System</span>
                                @endif
                            </td></tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Financial Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Opening Balance:</th><td>৳{{ number_format($account->opening_balance, 2) }}</td></tr>
                            <tr><th>Current Balance:</th><td><strong class="{{ $account->current_balance >= 0 ? 'text-success' : 'text-danger' }}">৳{{ number_format(abs($account->current_balance), 2) }}</strong></td></tr>
                        </table>
                    </div>
                </div>
                
                @if($account->description)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Description</h6>
                        <p class="bg-light p-3 rounded">{{ $account->description }}</p>
                    </div>
                </div>
                @endif
                
                @if($account->children->count() > 0)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-2 mb-3">Sub-Accounts</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($account->children as $child)
                                        <tr>
                                            <td><code>{{ $child->code }}</code></td>
                                            <td><a href="{{ route('accounts.show', $child) }}">{{ $child->name }}</a></td>
                                            <td>৳{{ number_format(abs($child->current_balance), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

