@extends('layouts.dashboard')

@section('title', 'Edit Payment')
@section('page-title', 'Edit Payment')

@section('content')
<form action="{{ route('payments.update', $payment) }}" method="POST">
@csrf
@method('PUT')
<div class="row">
    <div class="col-md-8">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-money-bill-wave me-2"></i>Edit Payment: {{ $payment->payment_number }}</h6>
                <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror"
                                   id="amount" name="amount" value="{{ old('amount', $payment->amount) }}" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror"
                               id="payment_date" name="payment_date"
                               value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                        @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                            <option value="cash" {{ old('payment_method', $payment->payment_method)=='cash'?'selected':'' }}>Cash</option>
                            <option value="card" {{ old('payment_method', $payment->payment_method)=='card'?'selected':'' }}>Card</option>
                            <option value="mobile_banking" {{ old('payment_method', $payment->payment_method)=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                            <option value="bank_transfer" {{ old('payment_method', $payment->payment_method)=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                            <option value="cheque" {{ old('payment_method', $payment->payment_method)=='cheque'?'selected':'' }}>Cheque</option>
                            <option value="other" {{ old('payment_method', $payment->payment_method)=='other'?'selected':'' }}>Other</option>
                        </select>
                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="reference_number" class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="reference_number" name="reference_number"
                               value="{{ old('reference_number', $payment->reference_number) }}"
                               placeholder="Transaction ID, Cheque Number, etc.">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Optional notes">{{ old('notes', $payment->notes) }}</textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-link me-2"></i>Linked Reference</h6>
            </div>
            <div class="p-4">
                @if($payment->payment_type === 'customer' && $payment->sale)
                    <div class="p-3 rounded" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-left:4px solid #16a34a;">
                        <div class="small text-muted mb-1">Invoice</div>
                        <a href="{{ route('sales.show', $payment->sale) }}" class="fw-bold text-decoration-none" style="color:#166534;">
                            {{ $payment->sale->invoice_number }}
                        </a>
                        <div class="small mt-1">Customer: {{ $payment->sale->customer->name ?? 'N/A' }}</div>
                        <div class="small">Total: ৳{{ number_format($payment->sale->total_amount, 2) }}</div>
                    </div>
                @elseif($payment->payment_type === 'customer' && $payment->service)
                    <div class="p-3 rounded" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-left:4px solid #16a34a;">
                        <div class="small text-muted mb-1">Service</div>
                        <a href="{{ route('services.show', $payment->service) }}" class="fw-bold text-decoration-none" style="color:#166534;">
                            {{ $payment->service->service_number }}
                        </a>
                        <div class="small mt-1">Customer: {{ $payment->service->customer_name }}</div>
                        <div class="small">Cost: ৳{{ number_format($payment->service->service_cost, 2) }}</div>
                    </div>
                @elseif($payment->payment_type === 'supplier' && $payment->purchase)
                    <div class="p-3 rounded" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-left:4px solid #3b82f6;">
                        <div class="small text-muted mb-1">Purchase Order</div>
                        <a href="{{ route('purchases.show', $payment->purchase) }}" class="fw-bold text-decoration-none" style="color:#1d4ed8;">
                            {{ $payment->purchase->po_number }}
                        </a>
                        <div class="small mt-1">Supplier: {{ $payment->purchase->supplier->name ?? 'N/A' }}</div>
                        <div class="small">Total: ৳{{ number_format($payment->purchase->total_amount, 2) }}</div>
                    </div>
                @else
                    <p class="text-muted small mb-0">No linked invoice or purchase order.</p>
                @endif
            </div>
        </div>
        <div class="table-card mt-3">
            <div class="p-4">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Payment</button>
                    <a href="{{ route('payments.show', $payment) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection
