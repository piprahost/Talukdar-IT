@extends('layouts.dashboard')

@section('title', 'Create Expense')
@section('page-title', 'Create Expense')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-plus-circle me-2"></i>Create New Expense</h6>
                <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="p-4">
                    <!-- Expense Details -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Expense Details</h6>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('expense_date') is-invalid @enderror" 
                                       id="expense_date" name="expense_date" 
                                       value="{{ old('expense_date', date('Y-m-d')) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror" 
                                       id="category" name="category" list="categoryList"
                                       value="{{ old('category') }}" required 
                                       placeholder="e.g., Office Supplies, Utilities, Rent">
                                <datalist id="categoryList">
                                    <option value="Office Supplies">
                                    <option value="Utilities">
                                    <option value="Rent">
                                    <option value="Transportation">
                                    <option value="Marketing">
                                    <option value="Salaries">
                                    <option value="Professional Services">
                                    <option value="Maintenance">
                                    <option value="Travel">
                                    <option value="Other">
                                </datalist>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="account_id" class="form-label">Expense Account</label>
                                <select class="form-select @error('account_id') is-invalid @enderror" 
                                        id="account_id" name="account_id">
                                    <option value="">Select Account (Optional)</option>
                                    @foreach($expenseAccounts as $account)
                                        <option value="{{ $account->id }}" {{ old('account_id')==$account->id?'selected':'' }}>
                                            {{ $account->code }} - {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Select the expense account from Chart of Accounts</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Amount (BDT) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" name="amount" 
                                       value="{{ old('amount') }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="draft" {{ old('status')=='draft'?'selected':'' }}>Draft</option>
                                    <option value="approved" {{ old('status')=='approved'?'selected':'' }}>Approved</option>
                                    <option value="paid" {{ old('status')=='paid'?'selected':'' }}>Paid</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" required 
                                      placeholder="Describe the expense...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Payment Information -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Payment Information</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_method') is-invalid @enderror" 
                                        id="payment_method" name="payment_method" required onchange="toggleBankAccount()">
                                    <option value="cash" {{ old('payment_method')=='cash'?'selected':'' }}>Cash</option>
                                    <option value="card" {{ old('payment_method')=='card'?'selected':'' }}>Card</option>
                                    <option value="mobile_banking" {{ old('payment_method')=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                                    <option value="bank_transfer" {{ old('payment_method')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                                    <option value="cheque" {{ old('payment_method')=='cheque'?'selected':'' }}>Cheque</option>
                                    <option value="other" {{ old('payment_method')=='other'?'selected':'' }}>Other</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3" id="bankAccountField" style="display: none;">
                                <label for="bank_account_id" class="form-label">Bank Account</label>
                                <select class="form-select @error('bank_account_id') is-invalid @enderror" 
                                        id="bank_account_id" name="bank_account_id">
                                    <option value="">Select Bank Account</option>
                                    @foreach($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}" {{ old('bank_account_id')==$bankAccount->id?'selected':'' }}>
                                            {{ $bankAccount->account_name }} - {{ $bankAccount->bank_name }} (৳{{ number_format($bankAccount->current_balance, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Required for bank transfer and card payments</small>
                            </div>
                            
                            <div class="col-md-6 mb-3" id="paymentDateField" style="display: none;">
                                <label for="payment_date" class="form-label">Payment Date</label>
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                       id="payment_date" name="payment_date" 
                                       value="{{ old('payment_date') }}">
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty to use expense date</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vendor Information -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Vendor Information (Optional)</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vendor_name" class="form-label">Vendor/Supplier Name</label>
                                <input type="text" class="form-control @error('vendor_name') is-invalid @enderror" 
                                       id="vendor_name" name="vendor_name" 
                                       value="{{ old('vendor_name') }}" 
                                       placeholder="Vendor or supplier name">
                                @error('vendor_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="vendor_contact" class="form-label">Vendor Contact</label>
                                <input type="text" class="form-control @error('vendor_contact') is-invalid @enderror" 
                                       id="vendor_contact" name="vendor_contact" 
                                       value="{{ old('vendor_contact') }}" 
                                       placeholder="Phone or email">
                                @error('vendor_contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference_number" class="form-label">Reference Number</label>
                                <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                       id="reference_number" name="reference_number" 
                                       value="{{ old('reference_number') }}" 
                                       placeholder="Invoice/Receipt number">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="attachment" class="form-label">Attachment</label>
                                <input type="file" class="form-control @error('attachment') is-invalid @enderror" 
                                       id="attachment" name="attachment" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Upload receipt or invoice (PDF, JPG, PNG - Max 5MB)</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Notes -->
                    <div class="mb-4">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="2" 
                                  placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Expense
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleBankAccount() {
    const paymentMethod = document.getElementById('payment_method').value;
    const bankAccountField = document.getElementById('bankAccountField');
    const paymentDateField = document.getElementById('paymentDateField');
    const statusField = document.getElementById('status').value;
    
    const needsBank = ['bank_transfer', 'card', 'mobile_banking', 'cheque'].includes(paymentMethod);
    bankAccountField.style.display = needsBank ? 'block' : 'none';
    
    // Show payment date field if status is paid
    if (statusField === 'paid') {
        paymentDateField.style.display = 'block';
    } else {
        paymentDateField.style.display = 'none';
    }
}

// Toggle payment date field based on status
document.getElementById('status').addEventListener('change', function() {
    const paymentDateField = document.getElementById('paymentDateField');
    if (this.value === 'paid') {
        paymentDateField.style.display = 'block';
    } else {
        paymentDateField.style.display = 'none';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleBankAccount();
});
</script>
@endpush

