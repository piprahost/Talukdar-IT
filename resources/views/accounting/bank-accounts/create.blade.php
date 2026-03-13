@extends('layouts.dashboard')

@section('title', 'Create Bank Account')
@section('page-title', 'Create Bank Account')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-university me-2"></i>Create Bank Account</h6>
                <a href="{{ route('bank-accounts.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('bank-accounts.store') }}" method="POST">
                @csrf
                
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="account_name" class="form-label">Account Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('account_name') is-invalid @enderror" 
                                   id="account_name" name="account_name" 
                                   value="{{ old('account_name') }}" 
                                   placeholder="e.g., Main Account, Savings" required>
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="bank_name" class="form-label">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                   id="bank_name" name="bank_name" 
                                   value="{{ old('bank_name') }}" 
                                   placeholder="e.g., BRAC Bank, DBBL" required>
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="account_number" class="form-label">Account Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('account_number') is-invalid @enderror" 
                                   id="account_number" name="account_number" 
                                   value="{{ old('account_number') }}" required>
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="branch_name" class="form-label">Branch Name</label>
                            <input type="text" class="form-control" 
                                   id="branch_name" name="branch_name" 
                                   value="{{ old('branch_name') }}">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="account_type" class="form-label">Account Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('account_type') is-invalid @enderror" 
                                    id="account_type" name="account_type" required>
                                <option value="checking" {{ old('account_type')=='checking'?'selected':'' }}>Checking</option>
                                <option value="savings" {{ old('account_type')=='savings'?'selected':'' }}>Savings</option>
                                <option value="fixed_deposit" {{ old('account_type')=='fixed_deposit'?'selected':'' }}>Fixed Deposit</option>
                            </select>
                            @error('account_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="routing_number" class="form-label">Routing Number</label>
                            <input type="text" class="form-control" 
                                   id="routing_number" name="routing_number" 
                                   value="{{ old('routing_number') }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="swift_code" class="form-label">SWIFT Code</label>
                            <input type="text" class="form-control" 
                                   id="swift_code" name="swift_code" 
                                   value="{{ old('swift_code') }}">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="opening_balance" class="form-label">Opening Balance (BDT)</label>
                            <input type="number" step="0.01" class="form-control" 
                                   id="opening_balance" name="opening_balance" 
                                   value="{{ old('opening_balance', 0) }}" min="0">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="account_id" class="form-label">Link to Chart of Account</label>
                            <select class="form-select" id="account_id" name="account_id">
                                <option value="">Auto Create</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('account_id')==$account->id?'selected':'' }}>
                                        {{ $account->code }} - {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('bank-accounts.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Bank Account
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

