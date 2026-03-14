@extends('layouts.dashboard')

@section('title', 'Add New Service Order')
@section('page-title', 'Add New Service Order')

@section('content')
<div class="service-form-wrap">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h5 class="mb-1 fw-bold">Add service order</h5>
            <p class="text-muted small mb-0">Product, customer, dates and payment.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('services.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
            <button type="submit" form="serviceForm" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Create service</button>
        </div>
    </div>

    <form action="{{ route('services.store') }}" method="POST" id="serviceForm">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="table-card mb-0">
                    <div class="table-card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-box me-2 text-primary"></i>Product</h6>
                    </div>
                    <div class="p-4 pt-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Product name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('product_name') is-invalid @enderror"
                                       name="product_name" id="product_name" value="{{ old('product_name') }}"
                                       placeholder="e.g. Laptop, Printer" required autofocus>
                                @error('product_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Serial / Barcode <button type="button" class="btn btn-link btn-sm p-0 ms-1" onclick="focusBarcodeField()" title="Ctrl+B"><i class="fas fa-barcode"></i></button></label>
                                <input type="text" class="form-control @error('serial_number') is-invalid @enderror"
                                       name="serial_number" id="serial_number" value="{{ old('serial_number') }}" placeholder="Scan or enter">
                                @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Problem description</label>
                                <textarea class="form-control form-control-sm @error('problem_notes') is-invalid @enderror"
                                          name="problem_notes" id="problem_notes" rows="2" placeholder="Customer reported issue...">{{ old('problem_notes') }}</textarea>
                                @error('problem_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Technician notes</label>
                                <textarea class="form-control form-control-sm @error('service_notes') is-invalid @enderror"
                                          name="service_notes" id="service_notes" rows="2" placeholder="Diagnosis, parts used...">{{ old('service_notes') }}</textarea>
                                @error('service_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-card mt-4">
                    <div class="table-card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-user me-2 text-primary"></i>Customer</h6>
                    </div>
                    <div class="p-4 pt-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                       name="customer_name" value="{{ old('customer_name') }}" required placeholder="Full name">
                                @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                       name="customer_phone" value="{{ old('customer_phone') }}" required placeholder="+880 1XXX XXXXXX">
                                @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Address</label>
                                <textarea class="form-control form-control-sm @error('customer_address') is-invalid @enderror"
                                          name="customer_address" rows="2" placeholder="Address...">{{ old('customer_address') }}</textarea>
                                @error('customer_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-card mt-4">
                    <div class="table-card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-sticky-note me-2 text-primary"></i>Internal notes</h6>
                    </div>
                    <div class="p-4 pt-3">
                        <textarea class="form-control form-control-sm @error('internal_notes') is-invalid @enderror"
                                  name="internal_notes" rows="2" placeholder="Private (not shown to customer)...">{{ old('internal_notes') }}</textarea>
                        @error('internal_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="service-form-sidebar">
                    <div class="table-card mb-4">
                        <div class="table-card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-calendar me-2 text-primary"></i>Dates & cost</h6>
                        </div>
                        <div class="p-4 pt-3">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Receive date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-sm @error('receive_date') is-invalid @enderror"
                                       name="receive_date" value="{{ old('receive_date', date('Y-m-d')) }}" required>
                                @error('receive_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Expected delivery</label>
                                <input type="date" class="form-control form-control-sm @error('delivery_date') is-invalid @enderror"
                                       name="delivery_date" value="{{ old('delivery_date') }}">
                                @error('delivery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-semibold small">Service cost (৳) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="0" class="form-control @error('service_cost') is-invalid @enderror"
                                           name="service_cost" id="service_cost" value="{{ old('service_cost', '0') }}" required oninput="recalculate()">
                                </div>
                                @error('service_cost')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="table-card mb-4">
                        <div class="table-card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-money-bill-wave me-2 text-primary"></i>Payment</h6>
                        </div>
                        <div class="p-4 pt-3">
                            <div class="p-3 rounded-3 mb-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Cost</span><strong id="preview_cost">৳0.00</strong></div>
                                <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Paid</span><strong class="text-success" id="preview_paid">৳0.00</strong></div>
                                <div class="d-flex justify-content-between small"><span class="text-muted">Due</span><strong id="preview_due" class="text-danger">৳0.00</strong></div>
                                <div class="text-center mt-1"><span id="preview_status" class="badge bg-danger" style="font-size:10px;">Unpaid</span></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Paid (৳) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="0" class="form-control @error('paid_amount') is-invalid @enderror"
                                           name="paid_amount" id="paid_amount" value="{{ old('paid_amount', '0') }}" required oninput="recalculate()">
                                </div>
                                @error('paid_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                <div class="d-flex gap-1 mt-1"><button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setFullPayment()">Full</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setHalfPayment()">Half</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setZeroPayment()">None</button></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Due (auto)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm bg-light" id="due_amount" value="{{ old('due_amount', '0') }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Payment method <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm @error('payment_method') is-invalid @enderror" name="payment_method" id="payment_method" required onchange="toggleBankAccount()">
                                    <option value="cash" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='cash'?'selected':'' }}>Cash</option>
                                    <option value="card" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='card'?'selected':'' }}>Card</option>
                                    <option value="mobile_banking" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                                    <option value="bank_transfer" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                                    <option value="cheque" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='cheque'?'selected':'' }}>Cheque</option>
                                    <option value="other" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='other'?'selected':'' }}>Other</option>
                                </select>
                                @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-0" id="bankAccountField" style="display:none;">
                                <label class="form-label fw-semibold small">Bank account</label>
                                <select class="form-select form-select-sm @error('bank_account_id') is-invalid @enderror" name="bank_account_id" id="bank_account_id">
                                    <option value="">Select account</option>
                                    @foreach($bankAccounts as $ba)
                                        <option value="{{ $ba->id }}" {{ old('bank_account_id')==$ba->id?'selected':'' }}>{{ $ba->account_name }} — {{ $ba->bank_name }}</option>
                                    @endforeach
                                </select>
                                @error('bank_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="table-card mb-4">
                        <div class="table-card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-tag me-2 text-primary"></i>Status</h6>
                        </div>
                        <div class="p-4 pt-3">
                            <select class="form-select form-select-sm @error('status') is-invalid @enderror" name="status" required>
                                <option value="pending" {{ old('status', $defaultStatus ?? 'pending')=='pending'?'selected':'' }}>Pending</option>
                                <option value="in_progress" {{ old('status', $defaultStatus ?? 'pending')=='in_progress'?'selected':'' }}>In progress</option>
                                <option value="completed" {{ old('status')=='completed'?'selected':'' }}>Completed</option>
                                <option value="delivered" {{ old('status')=='delivered'?'selected':'' }}>Delivered</option>
                                <option value="cancelled" {{ old('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-none d-lg-grid gap-2">
                        <button type="submit" form="serviceForm" class="btn btn-primary"><i class="fas fa-save me-2"></i>Create service order</button>
                        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>
function recalculate() {
    const cost = parseFloat(document.getElementById('service_cost').value) || 0;
    const paid = Math.min(parseFloat(document.getElementById('paid_amount').value) || 0, cost);
    const due  = Math.max(0, cost - paid);

    // Clamp paid input to cost
    if ((parseFloat(document.getElementById('paid_amount').value) || 0) > cost) {
        document.getElementById('paid_amount').value = cost.toFixed(2);
    }

    document.getElementById('due_amount').value = due.toFixed(2);

    // Preview strip
    document.getElementById('preview_cost').textContent = '৳' + cost.toFixed(2);
    document.getElementById('preview_paid').textContent = '৳' + paid.toFixed(2);
    document.getElementById('preview_due').textContent  = '৳' + due.toFixed(2);

    const badge = document.getElementById('preview_status');
    if (paid === 0) {
        badge.textContent = 'Unpaid';
        badge.className   = 'badge bg-danger';
        document.getElementById('preview_due').style.color = '#ef4444';
    } else if (due === 0) {
        badge.textContent = 'Paid';
        badge.className   = 'badge bg-success';
        document.getElementById('preview_due').style.color = '#16a34a';
    } else {
        badge.textContent = 'Partial';
        badge.className   = 'badge bg-warning text-dark';
        document.getElementById('preview_due').style.color = '#ef4444';
    }
}

function setFullPayment() {
    const cost = parseFloat(document.getElementById('service_cost').value) || 0;
    document.getElementById('paid_amount').value = cost.toFixed(2);
    recalculate();
}
function setHalfPayment() {
    const cost = parseFloat(document.getElementById('service_cost').value) || 0;
    document.getElementById('paid_amount').value = (cost / 2).toFixed(2);
    recalculate();
}
function setZeroPayment() {
    document.getElementById('paid_amount').value = '0.00';
    recalculate();
}

function toggleBankAccount() {
    const method = document.getElementById('payment_method').value;
    const show   = ['card','mobile_banking','bank_transfer','cheque'].includes(method);
    document.getElementById('bankAccountField').style.display = show ? 'block' : 'none';
}

function focusBarcodeField() {
    const field = document.getElementById('serial_number');
    field.focus();
    field.select();
    field.style.borderColor = '#16a34a';
    setTimeout(() => field.style.borderColor = '', 1500);
}

// Barcode scanner support (scanners send data very fast then Enter)
let barcodeBuffer = '', lastKeyTime = 0;
document.addEventListener('DOMContentLoaded', function () {
    recalculate();
    toggleBankAccount();

    const barcodeField = document.getElementById('serial_number');
    if (barcodeField) {
        barcodeField.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && this.value.length > 0) {
                e.preventDefault();
                this.style.backgroundColor = '#dcfce7';
                setTimeout(() => this.style.backgroundColor = '', 400);
                document.getElementById('problem_notes').focus();
            }
        });
        barcodeField.addEventListener('paste', function () {
            setTimeout(() => {
                this.style.backgroundColor = '#dcfce7';
                setTimeout(() => this.style.backgroundColor = '', 400);
                document.getElementById('problem_notes').focus();
            }, 10);
        });
    }

    // Ctrl+B shortcut to focus barcode field
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            focusBarcodeField();
        }
    });
});
</script>
@endpush
@endsection
