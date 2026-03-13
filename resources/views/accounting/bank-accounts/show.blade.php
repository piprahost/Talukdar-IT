@extends('layouts.dashboard')

@section('title', 'Bank Account Details')
@section('page-title', 'Bank Account Details')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-university me-2"></i>{{ $bankAccount->bank_name }} - {{ $bankAccount->account_name }}</h6>
                <div>
                    <a href="{{ route('bank-accounts.edit', $bankAccount) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                    <a href="{{ route('bank-accounts.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <div class="p-4">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h5 class="mb-0">Current Balance: 
                                <span class="{{ $bankAccount->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                    ৳{{ number_format($bankAccount->current_balance, 2) }}
                                </span>
                            </h5>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Account Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Account Name:</th><td><strong>{{ $bankAccount->account_name }}</strong></td></tr>
                            <tr><th>Bank Name:</th><td>{{ $bankAccount->bank_name }}</td></tr>
                            <tr><th>Account Number:</th><td><code>{{ $bankAccount->account_number }}</code></td></tr>
                            <tr><th>Branch Name:</th><td>{{ $bankAccount->branch_name ?? 'N/A' }}</td></tr>
                            <tr><th>Account Type:</th><td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $bankAccount->account_type)) }}</span></td></tr>
                            <tr><th>Status:</th><td><span class="badge {{ $bankAccount->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $bankAccount->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Financial Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Opening Balance:</th><td>৳{{ number_format($bankAccount->opening_balance, 2) }}</td></tr>
                            <tr><th>Current Balance:</th><td><strong class="{{ $bankAccount->current_balance >= 0 ? 'text-success' : 'text-danger' }}">৳{{ number_format($bankAccount->current_balance, 2) }}</strong></td></tr>
                            @if($bankAccount->routing_number)
                            <tr><th>Routing Number:</th><td>{{ $bankAccount->routing_number }}</td></tr>
                            @endif
                            @if($bankAccount->swift_code)
                            <tr><th>SWIFT Code:</th><td>{{ $bankAccount->swift_code }}</td></tr>
                            @endif
                            @if($bankAccount->account)
                            <tr><th>Linked Account:</th><td>{{ $bankAccount->account->code }} - {{ $bankAccount->account->name }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                @if($bankAccount->notes)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Notes</h6>
                        <p class="bg-light p-3 rounded">{{ $bankAccount->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

