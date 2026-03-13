@extends('layouts.dashboard')

@section('title', 'Edit Payment')
@section('page-title', 'Edit Payment')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-money-bill-wave me-2"></i>Edit Payment: {{ $payment->payment_number }}</h6>
                <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('payments.update', $payment) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="p-4">
                    <div class="alert alert-info">
                        <strong>Reference:</strong> 
                        @if($payment->payment_type === 'customer' && $payment->sale)
                            Invoice: {{ $payment->sale->invoice_number }}
                        @elseif($payment->payment_type === 'supplier' && $payment->purchase)
                            PO: {{ $payment->purchase->po_number }}
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                               id="amount" name="amount" value="{{ old('amount', $payment->amount) }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                               id="payment_date" name="payment_date" 
                               value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                            <option value="cash" {{ old('payment_method', $payment->payment_method)=='cash'?'selected':'' }}>Cash</option>
                            <option value="card" {{ old('payment_method', $payment->payment_method)=='card'?'selected':'' }}>Card</option>
                            <option value="mobile_banking" {{ old('payment_method', $payment->payment_method)=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                            <option value="bank_transfer" {{ old('payment_method', $payment->payment_method)=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                            <option value="cheque" {{ old('payment_method', $payment->payment_method)=='cheque'?'selected':'' }}>Cheque</option>
                            <option value="other" {{ old('payment_method', $payment->payment_method)=='other'?'selected':'' }}>Other</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="reference_number" class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="reference_number" name="reference_number" 
                               value="{{ old('reference_number', $payment->reference_number) }}" 
                               placeholder="Transaction ID, Cheque Number, etc.">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $payment->notes) }}</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('payments.show', $payment) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Payment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

