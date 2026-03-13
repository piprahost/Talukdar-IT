@extends('layouts.dashboard')

@section('title', 'Create Journal Entry')
@section('page-title', 'Create Journal Entry')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-book-open me-2"></i>Create Journal Entry</h6>
                <a href="{{ route('journal-entries.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('journal-entries.store') }}" method="POST" id="journalForm">
                @csrf
                
                <div class="p-4">
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label for="entry_date" class="form-label">Entry Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('entry_date') is-invalid @enderror" 
                                   id="entry_date" name="entry_date" 
                                   value="{{ old('entry_date', date('Y-m-d')) }}" required>
                            @error('entry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="reference" class="form-label">Reference</label>
                            <input type="text" class="form-control" 
                                   id="reference" name="reference" 
                                   value="{{ old('reference') }}" 
                                   placeholder="Reference number or document">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="2" required 
                                  placeholder="Entry description...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Journal Entry Items</h6>
                        <div class="table-responsive">
                            <table class="table table-hover" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 35%;">Account <span class="text-danger">*</span></th>
                                        <th style="width: 25%;">Debit (BDT)</th>
                                        <th style="width: 25%;">Credit (BDT)</th>
                                        <th style="width: 15%;">Description</th>
                                        <th style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    <tr class="entry-row">
                                        <td>
                                            <select class="form-select account-select" name="items[0][account_id]" required>
                                                <option value="">Select Account</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control debit-input" 
                                                   name="items[0][debit]" value="0" min="0" 
                                                   onchange="calculateTotals()">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control credit-input" 
                                                   name="items[0][credit]" value="0" min="0" 
                                                   onchange="calculateTotals()">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" 
                                                   name="items[0][description]" 
                                                   placeholder="Optional">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-row" onclick="removeRow(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="entry-row">
                                        <td>
                                            <select class="form-select account-select" name="items[1][account_id]" required>
                                                <option value="">Select Account</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control debit-input" 
                                                   name="items[1][debit]" value="0" min="0" 
                                                   onchange="calculateTotals()">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control credit-input" 
                                                   name="items[1][credit]" value="0" min="0" 
                                                   onchange="calculateTotals()">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" 
                                                   name="items[1][description]" 
                                                   placeholder="Optional">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-row" onclick="removeRow(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-secondary">
                                        <th colspan="1" class="text-end">Total:</th>
                                        <th id="totalDebit">৳0.00</th>
                                        <th id="totalCredit">৳0.00</th>
                                        <th colspan="2">
                                            <span id="balanceStatus" class="badge"></span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addRow()">
                            <i class="fas fa-plus me-2"></i>Add Row
                        </button>
                        @error('items')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Create Entry
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let rowIndex = 2;

function addRow() {
    const tbody = document.getElementById('itemsBody');
    const newRow = document.createElement('tr');
    newRow.className = 'entry-row';
    newRow.innerHTML = `
        <td>
            <select class="form-select account-select" name="items[${rowIndex}][account_id]" required>
                <option value="">Select Account</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" step="0.01" class="form-control debit-input" 
                   name="items[${rowIndex}][debit]" value="0" min="0" 
                   onchange="calculateTotals()">
        </td>
        <td>
            <input type="number" step="0.01" class="form-control credit-input" 
                   name="items[${rowIndex}][credit]" value="0" min="0" 
                   onchange="calculateTotals()">
        </td>
        <td>
            <input type="text" class="form-control" 
                   name="items[${rowIndex}][description]" 
                   placeholder="Optional">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-row" onclick="removeRow(this)">
                <i class="fas fa-times"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    rowIndex++;
}

function removeRow(button) {
    const row = button.closest('tr');
    if (document.querySelectorAll('.entry-row').length > 2) {
        row.remove();
        calculateTotals();
    } else {
        alert('At least 2 rows are required for a journal entry.');
    }
}

function calculateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;
    
    document.querySelectorAll('.debit-input').forEach(input => {
        totalDebit += parseFloat(input.value) || 0;
    });
    
    document.querySelectorAll('.credit-input').forEach(input => {
        totalCredit += parseFloat(input.value) || 0;
    });
    
    document.getElementById('totalDebit').textContent = '৳' + totalDebit.toFixed(2);
    document.getElementById('totalCredit').textContent = '৳' + totalCredit.toFixed(2);
    
    const difference = Math.abs(totalDebit - totalCredit);
    const balanceStatus = document.getElementById('balanceStatus');
    const submitBtn = document.getElementById('submitBtn');
    
    if (difference < 0.01) {
        balanceStatus.className = 'badge bg-success';
        balanceStatus.textContent = 'Balanced';
        submitBtn.disabled = false;
    } else {
        balanceStatus.className = 'badge bg-danger';
        balanceStatus.textContent = 'Difference: ৳' + difference.toFixed(2);
        submitBtn.disabled = true;
    }
}

// Prevent both debit and credit being entered for same row
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('debit-input')) {
            if (parseFloat(e.target.value) > 0) {
                e.target.closest('tr').querySelector('.credit-input').value = 0;
                calculateTotals();
            }
        }
        if (e.target.classList.contains('credit-input')) {
            if (parseFloat(e.target.value) > 0) {
                e.target.closest('tr').querySelector('.debit-input').value = 0;
                calculateTotals();
            }
        }
    });
});
</script>
@endpush
@endsection

