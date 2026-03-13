@extends('layouts.dashboard')

@section('title', 'Add New Service Order')
@section('page-title', 'Add New Service Order')

@section('content')
<div class="row">
    <div class="col-xl-10 mx-auto">
        <form action="{{ route('services.store') }}" method="POST" id="serviceForm">
            @csrf
            <div class="row g-3">

                {{-- ── Left Column: Product + Customer ── --}}
                <div class="col-md-8">

                    {{-- Product Information --}}
                    <div class="table-card mb-3">
                        <div class="table-card-header">
                            <h6><i class="fas fa-box me-2"></i>Product Information</h6>
                        </div>
                        <div class="p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('product_name') is-invalid @enderror"
                                           name="product_name" id="product_name"
                                           value="{{ old('product_name') }}"
                                           placeholder="e.g. Laptop, Printer, Desktop" required autofocus>
                                    @error('product_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Serial / Barcode
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-1 py-0 px-2"
                                                onclick="focusBarcodeField()" title="Focus for barcode (Ctrl+B)">
                                            <i class="fas fa-barcode"></i>
                                        </button>
                                    </label>
                                    <input type="text" class="form-control @error('serial_number') is-invalid @enderror"
                                           name="serial_number" id="serial_number"
                                           value="{{ old('serial_number') }}"
                                           placeholder="Scan or enter serial number">
                                    @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text"><i class="fas fa-info-circle me-1"></i>Press Ctrl+B to focus for scanning</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Problem Description</label>
                                    <textarea class="form-control @error('problem_notes') is-invalid @enderror"
                                              name="problem_notes" id="problem_notes" rows="3"
                                              placeholder="Describe the problem reported by the customer...">{{ old('problem_notes') }}</textarea>
                                    @error('problem_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Technician Notes</label>
                                    <textarea class="form-control @error('service_notes') is-invalid @enderror"
                                              name="service_notes" id="service_notes" rows="3"
                                              placeholder="Internal technician notes, diagnosis, parts used...">{{ old('service_notes') }}</textarea>
                                    @error('service_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Information --}}
                    <div class="table-card mb-3">
                        <div class="table-card-header">
                            <h6><i class="fas fa-user me-2"></i>Customer Information</h6>
                        </div>
                        <div class="p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                           name="customer_name" value="{{ old('customer_name') }}" required
                                           placeholder="Full name">
                                    @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                           name="customer_phone" value="{{ old('customer_phone') }}" required
                                           placeholder="+880 1XXX XXXXXX">
                                    @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control @error('customer_address') is-invalid @enderror"
                                              name="customer_address" rows="2"
                                              placeholder="Customer address...">{{ old('customer_address') }}</textarea>
                                    @error('customer_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Internal Notes --}}
                    <div class="table-card">
                        <div class="table-card-header">
                            <h6><i class="fas fa-sticky-note me-2"></i>Internal Notes <small class="text-muted fw-normal">(not visible to customer)</small></h6>
                        </div>
                        <div class="p-4">
                            <textarea class="form-control @error('internal_notes') is-invalid @enderror"
                                      name="internal_notes" rows="3"
                                      placeholder="Private notes for internal use only...">{{ old('internal_notes') }}</textarea>
                            @error('internal_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- ── Right Column: Dates, Cost, Payment, Status ── --}}
                <div class="col-md-4">

                    {{-- Dates & Cost --}}
                    <div class="table-card mb-3">
                        <div class="table-card-header">
                            <h6><i class="fas fa-calendar me-2"></i>Dates & Cost</h6>
                        </div>
                        <div class="p-4">
                            <div class="mb-3">
                                <label class="form-label">Receive Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('receive_date') is-invalid @enderror"
                                       name="receive_date" value="{{ old('receive_date', date('Y-m-d')) }}" required>
                                @error('receive_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Expected Delivery</label>
                                <input type="date" class="form-control @error('delivery_date') is-invalid @enderror"
                                       name="delivery_date" value="{{ old('delivery_date') }}">
                                @error('delivery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Service Cost (BDT) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text fw-bold">৳</span>
                                    <input type="number" step="0.01" min="0"
                                           class="form-control @error('service_cost') is-invalid @enderror"
                                           name="service_cost" id="service_cost"
                                           value="{{ old('service_cost', '0') }}" required
                                           oninput="recalculate()">
                                </div>
                                @error('service_cost')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Payment Information --}}
                    <div class="table-card mb-3">
                        <div class="table-card-header">
                            <h6><i class="fas fa-money-bill-wave me-2"></i>Payment</h6>
                        </div>
                        <div class="p-4">

                            {{-- Live payment summary strip --}}
                            <div class="d-flex justify-content-between mb-3 p-2 rounded" style="background:#f9fafb;border:1px solid #e5e7eb;font-size:12px;">
                                <div class="text-center">
                                    <div class="text-muted">Cost</div>
                                    <div class="fw-bold" id="preview_cost">৳0.00</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-muted">Paid</div>
                                    <div class="fw-bold text-success" id="preview_paid">৳0.00</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-muted">Due</div>
                                    <div class="fw-bold text-danger" id="preview_due">৳0.00</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-muted">Status</div>
                                    <div id="preview_status" class="badge bg-danger" style="font-size:10px;">Unpaid</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Paid Amount (BDT) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text fw-bold">৳</span>
                                    <input type="number" step="0.01" min="0"
                                           class="form-control @error('paid_amount') is-invalid @enderror"
                                           name="paid_amount" id="paid_amount"
                                           value="{{ old('paid_amount', '0') }}" required
                                           oninput="recalculate()">
                                </div>
                                @error('paid_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                <div class="d-flex gap-2 mt-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setFullPayment()">Full</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setHalfPayment()">Half</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setZeroPayment()">None</button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Due Amount (BDT)</label>
                                <div class="input-group">
                                    <span class="input-group-text fw-bold">৳</span>
                                    <input type="number" step="0.01" class="form-control"
                                           id="due_amount" value="{{ old('due_amount', '0') }}"
                                           readonly style="background:#f8f9fa;">
                                </div>
                                <div class="form-text">Auto-calculated</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_method') is-invalid @enderror"
                                        name="payment_method" id="payment_method" required onchange="toggleBankAccount()">
                                    <option value="cash"           {{ old('payment_method','cash')=='cash'           ?'selected':'' }}>Cash</option>
                                    <option value="card"           {{ old('payment_method')=='card'           ?'selected':'' }}>Card</option>
                                    <option value="mobile_banking" {{ old('payment_method')=='mobile_banking' ?'selected':'' }}>Mobile Banking</option>
                                    <option value="bank_transfer"  {{ old('payment_method')=='bank_transfer'  ?'selected':'' }}>Bank Transfer</option>
                                    <option value="cheque"         {{ old('payment_method')=='cheque'         ?'selected':'' }}>Cheque</option>
                                    <option value="other"          {{ old('payment_method')=='other'          ?'selected':'' }}>Other</option>
                                </select>
                                @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-0" id="bankAccountField" style="display:none;">
                                <label class="form-label">Bank Account <span class="text-danger">*</span></label>
                                <select class="form-select @error('bank_account_id') is-invalid @enderror"
                                        name="bank_account_id" id="bank_account_id">
                                    <option value="">— Select Bank Account —</option>
                                    @foreach($bankAccounts as $ba)
                                        <option value="{{ $ba->id }}" {{ old('bank_account_id')==$ba->id?'selected':'' }}>
                                            {{ $ba->account_name }} — {{ $ba->bank_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="table-card mb-3">
                        <div class="table-card-header">
                            <h6><i class="fas fa-tag me-2"></i>Service Status</h6>
                        </div>
                        <div class="p-4">
                            <select class="form-select @error('status') is-invalid @enderror"
                                    name="status" required>
                                <option value="pending"     {{ old('status','pending')=='pending'     ?'selected':'' }}>⏳ Pending</option>
                                <option value="in_progress" {{ old('status')=='in_progress' ?'selected':'' }}>🔧 In Progress</option>
                                <option value="completed"   {{ old('status')=='completed'   ?'selected':'' }}>✅ Completed</option>
                                <option value="delivered"   {{ old('status')=='delivered'   ?'selected':'' }}>📦 Delivered</option>
                                <option value="cancelled"   {{ old('status')=='cancelled'   ?'selected':'' }}>❌ Cancelled</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Create Service Order
                        </button>
                        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>
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
