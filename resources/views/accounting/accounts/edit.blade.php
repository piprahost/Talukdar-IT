@extends('layouts.dashboard')

@section('title', 'Edit Account')
@section('page-title', 'Edit Account')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-book me-2"></i>Edit Account</h6>
                <a href="{{ route('accounts.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('accounts.update', $account) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Account Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" value="{{ old('code', $account->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Account Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $account->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Account Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required onchange="updateCategories()">
                                <option value="asset" {{ old('type', $account->type)=='asset'?'selected':'' }}>Asset</option>
                                <option value="liability" {{ old('type', $account->type)=='liability'?'selected':'' }}>Liability</option>
                                <option value="equity" {{ old('type', $account->type)=='equity'?'selected':'' }}>Equity</option>
                                <option value="revenue" {{ old('type', $account->type)=='revenue'?'selected':'' }}>Revenue</option>
                                <option value="expense" {{ old('type', $account->type)=='expense'?'selected':'' }}>Expense</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="{{ $account->category }}">{{ ucfirst(str_replace('_', ' ', $account->category)) }}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="parent_id" class="form-label">Parent Account</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">None (Root Account)</option>
                                @foreach($parentAccounts as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $account->parent_id)==$parent->id?'selected':'' }}>
                                        {{ $parent->code }} - {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="balance_type" class="form-label">Balance Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="balance_type" name="balance_type" required>
                                <option value="debit" {{ old('balance_type', $account->balance_type)=='debit'?'selected':'' }}>Debit</option>
                                <option value="credit" {{ old('balance_type', $account->balance_type)=='credit'?'selected':'' }}>Credit</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="opening_balance" class="form-label">Opening Balance (BDT)</label>
                        <input type="number" step="0.01" class="form-control" 
                               id="opening_balance" name="opening_balance" 
                               value="{{ old('opening_balance', $account->opening_balance) }}" min="0">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $account->description) }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $account->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Account
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const categories = {
    asset: ['current_asset', 'fixed_asset', 'intangible_asset'],
    liability: ['current_liability', 'long_term_liability'],
    equity: ['capital', 'retained_earnings', 'drawing'],
    revenue: ['sales_revenue', 'other_revenue'],
    expense: ['cost_of_goods_sold', 'operating_expense', 'financial_expense', 'other_expense']
};

function updateCategories() {
    const type = document.getElementById('type').value;
    const categorySelect = document.getElementById('category');
    const currentCategory = '{{ $account->category }}';
    
    categorySelect.innerHTML = '';
    
    if (type && categories[type]) {
        const labels = {
            current_asset: 'Current Asset',
            fixed_asset: 'Fixed Asset',
            intangible_asset: 'Intangible Asset',
            current_liability: 'Current Liability',
            long_term_liability: 'Long Term Liability',
            capital: 'Capital',
            retained_earnings: 'Retained Earnings',
            drawing: 'Drawing',
            sales_revenue: 'Sales Revenue',
            other_revenue: 'Other Revenue',
            cost_of_goods_sold: 'Cost of Goods Sold',
            operating_expense: 'Operating Expense',
            financial_expense: 'Financial Expense',
            other_expense: 'Other Expense'
        };
        
        categories[type].forEach(cat => {
            const option = document.createElement('option');
            option.value = cat;
            option.textContent = labels[cat] || cat;
            if (cat === currentCategory) option.selected = true;
            categorySelect.appendChild(option);
        });
    }
}

document.addEventListener('DOMContentLoaded', updateCategories);
</script>
@endpush
@endsection

