@extends('layouts.dashboard')

@section('title', 'Edit Expense')
@section('page-title', 'Edit Expense')

@section('content')
<form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="row">
    <div class="col-md-8">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-edit me-2"></i>Edit Expense: {{ $expense->expense_number }}</h6>
                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            <div class="p-4">
                <!-- Expense Details -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Expense Details</h6>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('expense_date') is-invalid @enderror" 
                                       id="expense_date" name="expense_date" 
                                       value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror" 
                                       id="category" name="category" list="categoryList"
                                       value="{{ old('category', $expense->category) }}" required>
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
                                        <option value="{{ $account->id }}" {{ old('account_id', $expense->account_id)==$account->id?'selected':'' }}>
                                            {{ $account->code }} - {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Amount (BDT) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" name="amount" 
                                       value="{{ old('amount', $expense->amount) }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="draft" {{ old('status', $expense->status)=='draft'?'selected':'' }}>Draft</option>
                                    <option value="approved" {{ old('status', $expense->status)=='approved'?'selected':'' }}>Approved</option>
                                    <option value="paid" {{ old('status', $expense->status)=='paid'?'selected':'' }}>Paid</option>
                                    <option value="cancelled" {{ old('status', $expense->status)=='cancelled'?'selected':'' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" required>{{ old('description', $expense->description) }}</textarea>
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
                                    <option value="cash" {{ old('payment_method', $expense->payment_method)=='cash'?'selected':'' }}>Cash</option>
                                    <option value="card" {{ old('payment_method', $expense->payment_method)=='card'?'selected':'' }}>Card</option>
                                    <option value="mobile_banking" {{ old('payment_method', $expense->payment_method)=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                                    <option value="bank_transfer" {{ old('payment_method', $expense->payment_method)=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                                    <option value="cheque" {{ old('payment_method', $expense->payment_method)=='cheque'?'selected':'' }}>Cheque</option>
                                    <option value="other" {{ old('payment_method', $expense->payment_method)=='other'?'selected':'' }}>Other</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            @php $showBank = in_array(old('payment_method', $expense->payment_method), ['card', 'bank_transfer', 'mobile_banking', 'cheque']); @endphp
                            <div class="col-md-6 mb-3" id="bankAccountField" style="display: {{ $showBank ? 'block' : 'none' }};">
                                <label for="bank_account_id" class="form-label">Bank Account</label>
                                <select class="form-select @error('bank_account_id') is-invalid @enderror" 
                                        id="bank_account_id" name="bank_account_id">
                                    <option value="">Select Bank Account</option>
                                    @foreach($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}" {{ old('bank_account_id', $expense->bank_account_id)==$bankAccount->id?'selected':'' }}>
                                            {{ $bankAccount->account_name }} - {{ $bankAccount->bank_name }} (৳{{ number_format($bankAccount->current_balance, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3" id="paymentDateField" style="display: {{ old('status', $expense->status)=='paid' ? 'block' : 'none' }};">
                                <label for="payment_date" class="form-label">Payment Date</label>
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                       id="payment_date" name="payment_date" 
                                       value="{{ old('payment_date', $expense->payment_date?->format('Y-m-d')) }}">
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-store me-2"></i>Vendor & Notes</h6>
            </div>
            <div class="p-4">
                <div class="mb-3">
                    <label for="vendor_name" class="form-label">Vendor/Supplier Name</label>
                    <input type="text" class="form-control @error('vendor_name') is-invalid @enderror"
                           id="vendor_name" name="vendor_name" value="{{ old('vendor_name', $expense->vendor_name) }}" placeholder="Optional">
                    @error('vendor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="vendor_contact" class="form-label">Vendor Contact</label>
                    <input type="text" class="form-control @error('vendor_contact') is-invalid @enderror"
                           id="vendor_contact" name="vendor_contact" value="{{ old('vendor_contact', $expense->vendor_contact) }}" placeholder="Phone or email">
                    @error('vendor_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="reference_number" class="form-label">Reference Number</label>
                    <input type="text" class="form-control @error('reference_number') is-invalid @enderror"
                           id="reference_number" name="reference_number" value="{{ old('reference_number', $expense->reference_number) }}" placeholder="Invoice/Receipt #">
                    @error('reference_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="attachment" class="form-label">Attachment</label>
                    @if($expense->attachment)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt me-1"></i>View Current
                            </a>
                        </div>
                    @endif
                    <input type="file" class="form-control @error('attachment') is-invalid @enderror"
                           id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png">
                    @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">New file replaces existing</small>
                </div>
                <div class="mb-4">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', $expense->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Expense</button>
                    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
function toggleBankAccount() {
    const paymentMethod = document.getElementById('payment_method').value;
    const bankAccountField = document.getElementById('bankAccountField');
    const paymentDateField = document.getElementById('paymentDateField');
    const statusField = document.getElementById('status').value;
    
    var needsBank = ['bank_transfer', 'card', 'mobile_banking', 'cheque'].indexOf(paymentMethod) !== -1;
    bankAccountField.style.display = needsBank ? 'block' : 'none';
    
    if (statusField === 'paid') {
        paymentDateField.style.display = 'block';
    } else {
        paymentDateField.style.display = 'none';
    }
}

document.getElementById('status').addEventListener('change', function() {
    const paymentDateField = document.getElementById('paymentDateField');
    if (this.value === 'paid') {
        paymentDateField.style.display = 'block';
    } else {
        paymentDateField.style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    toggleBankAccount();
});
</script>
@endpush

