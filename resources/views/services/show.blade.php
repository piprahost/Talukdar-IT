@extends('layouts.dashboard')

@section('title', 'Service Order ' . $service->service_number)
@section('page-title', 'Service Order ' . $service->service_number)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('services.index') }}">Service Orders</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $service->service_number }}</li>
@endsection

@section('content')
@php
    $statusMap = [
        'pending'     => ['bg'=>'#fef3c7','color'=>'#92400e','dot'=>'#f59e0b','label'=>'Pending'],
        'in_progress' => ['bg'=>'#dbeafe','color'=>'#1e40af','dot'=>'#3b82f6','label'=>'In Progress'],
        'completed'   => ['bg'=>'#f0fdf4','color'=>'#166534','dot'=>'#22c55e','label'=>'Completed'],
        'delivered'   => ['bg'=>'#eff6ff','color'=>'#1d4ed8','dot'=>'#2563eb','label'=>'Delivered'],
        'cancelled'   => ['bg'=>'#fef2f2','color'=>'#991b1b','dot'=>'#ef4444','label'=>'Cancelled'],
    ];
    $sc = $statusMap[$service->status] ?? ['bg'=>'#f3f4f6','color'=>'#374151','dot'=>'#9ca3af','label'=>ucfirst($service->status)];
@endphp

<div class="row g-3">
    {{-- Top action bar --}}
    <div class="col-12">
        <div class="table-card p-3 p-md-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-bold" style="font-size:1.1rem;color:#111;">{{ $service->service_number }}</span>
                <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;">
                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $sc['dot'] }};display:inline-block;"></span>
                    {{ $sc['label'] }}
                </span>
                <span class="text-muted" style="font-size:13px;">
                    <i class="fas fa-calendar-alt me-1"></i>Received {{ $service->receive_date->format('d M Y') }}
                </span>
                @if($service->delivery_date)
                    <span class="text-muted" style="font-size:13px;">
                        <i class="fas fa-truck me-1"></i>Delivery {{ $service->delivery_date->format('d M Y') }}
                        @php $overdue = $service->delivery_date->isPast() && !in_array($service->status, ['delivered','cancelled']); @endphp
                        @if($overdue)<small class="text-danger fw-semibold ms-1"><i class="fas fa-exclamation-circle"></i> Overdue</small>@endif
                    </span>
                @endif
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if(in_array($service->status, ['completed', 'delivered']))
                    <a href="{{ route('service-returns.create', ['service_id' => $service->id]) }}" class="btn btn-sm btn-outline-danger"><i class="fas fa-undo me-1"></i>Return Service</a>
                @endif
                @if($service->due_amount > 0)
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#collectPaymentModal"><i class="fas fa-hand-holding-usd me-1"></i>Collect Payment</button>
                @endif
                <a href="{{ route('services.print', $service) }}" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="fas fa-print me-1"></i>Print</a>
                <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit me-1"></i>Edit</a>
                <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="col-12">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Service cost</div>
                    <div style="font-size:20px;font-weight:800;color:#111;">৳{{ number_format($service->service_cost, 2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100" style="border-left:3px solid #16a34a;">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Paid</div>
                    <div style="font-size:20px;font-weight:800;color:#16a34a;">৳{{ number_format($service->paid_amount, 2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100" style="border-left:3px solid {{ $service->due_amount > 0 ? '#ef4444' : '#16a34a' }};">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Due</div>
                    <div style="font-size:20px;font-weight:800;color:{{ $service->due_amount > 0 ? '#ef4444' : '#16a34a' }};">৳{{ number_format($service->due_amount, 2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Payment</div>
                    @if($service->payment_status === 'fully_paid')
                        <span style="display:inline-flex;align-items:center;gap:6px;background:#f0fdf4;color:#166534;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;"><i class="fas fa-check-circle"></i> Fully Paid</span>
                    @elseif($service->payment_status === 'partial')
                        <span style="display:inline-flex;align-items:center;gap:6px;background:#fef9c3;color:#854d0e;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;"><i class="fas fa-clock"></i> Partial</span>
                    @else
                        <span style="display:inline-flex;align-items:center;gap:6px;background:#fef2f2;color:#991b1b;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;"><i class="fas fa-times-circle"></i> Unpaid</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Main content: two columns --}}
    <div class="col-lg-8">
        {{-- Product & Service --}}
        <div class="table-card mb-3">
            <div class="table-card-header" style="border-left:3px solid #16a34a;">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-laptop-medical me-2" style="color:#16a34a;"></i>Product & Service</h6>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <dl class="row mb-0 g-2">
                            <dt class="col-sm-4 text-muted small text-uppercase fw-semibold">Product</dt>
                            <dd class="col-sm-8 fw-semibold">{{ $service->product_name }}</dd>
                            @if($service->serial_number)
                                <dt class="col-sm-4 text-muted small text-uppercase fw-semibold">Serial</dt>
                                <dd class="col-sm-8"><code class="bg-light px-2 py-1 rounded small">{{ $service->serial_number }}</code></dd>
                            @endif
                            @if($service->problem_notes)
                                <dt class="col-sm-4 text-muted small text-uppercase fw-semibold align-top">Problem</dt>
                                <dd class="col-sm-8" style="white-space:pre-line;">{{ $service->problem_notes }}</dd>
                            @endif
                            @if($service->service_notes)
                                <dt class="col-sm-4 text-muted small text-uppercase fw-semibold align-top">Technician</dt>
                                <dd class="col-sm-8" style="white-space:pre-line;">{{ $service->service_notes }}</dd>
                            @endif
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0 g-2">
                            <dt class="col-sm-4 text-muted small text-uppercase fw-semibold">Customer</dt>
                            <dd class="col-sm-8 fw-semibold">{{ $service->customer_name }}</dd>
                            <dt class="col-sm-4 text-muted small text-uppercase fw-semibold">Phone</dt>
                            <dd class="col-sm-8">
                                <a href="tel:{{ $service->customer_phone }}" class="text-decoration-none"><i class="fas fa-phone fa-xs me-1 text-muted"></i>{{ $service->customer_phone }}</a>
                            </dd>
                            @if($service->customer_address)
                                <dt class="col-sm-4 text-muted small text-uppercase fw-semibold align-top">Address</dt>
                                <dd class="col-sm-8">{{ $service->customer_address }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dates & Payment --}}
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-calendar-check me-2 text-primary"></i>Dates & Payment</h6>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <dl class="row mb-0 g-2">
                            <dt class="col-sm-5 text-muted small text-uppercase fw-semibold">Received</dt>
                            <dd class="col-sm-7">{{ $service->receive_date->format('d M Y') }}</dd>
                            <dt class="col-sm-5 text-muted small text-uppercase fw-semibold">Delivery</dt>
                            <dd class="col-sm-7">
                                @if($service->delivery_date)
                                    @php $overdue = $service->delivery_date->isPast() && !in_array($service->status, ['delivered','cancelled']); @endphp
                                    <span class="{{ $overdue ? 'text-danger fw-semibold' : '' }}">{{ $service->delivery_date->format('d M Y') }}@if($overdue) <i class="fas fa-exclamation-circle ms-1"></i>@endif</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </dd>
                            <dt class="col-sm-5 text-muted small text-uppercase fw-semibold">Created</dt>
                            <dd class="col-sm-7">{{ $service->created_at->format('d M Y, h:i A') }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0 g-2">
                            <dt class="col-sm-5 text-muted small text-uppercase fw-semibold">Method</dt>
                            <dd class="col-sm-7"><span class="badge bg-light text-dark fw-semibold">{{ $service->payment_method_label }}</span></dd>
                            @if($service->bankAccount)
                                <dt class="col-sm-5 text-muted small text-uppercase fw-semibold">Bank</dt>
                                <dd class="col-sm-7">{{ $service->bankAccount->account_name }} — {{ $service->bankAccount->bank_name }}</dd>
                            @endif
                            @if($service->created_by && $service->relationLoaded('creator') && $service->creator)
                                <dt class="col-sm-5 text-muted small text-uppercase fw-semibold">Created by</dt>
                                <dd class="col-sm-7">{{ $service->creator->name }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        @if($service->internal_notes)
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-sticky-note me-2 text-warning"></i>Internal notes</h6>
            </div>
            <div class="p-4">
                <p class="text-muted mb-0 small" style="white-space:pre-line;">{{ $service->internal_notes }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Invoice summary --}}
        <div class="table-card mb-3">
            <div class="table-card-header" style="background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%);border-left:3px solid #16a34a;">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-receipt me-2" style="color:#16a34a;"></i>Invoice summary</h6>
            </div>
            <div class="p-4">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Service cost</span>
                    <span class="fw-semibold">৳{{ number_format($service->service_cost, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Paid</span>
                    <span class="fw-semibold text-success">৳{{ number_format($service->paid_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between py-3 mt-2 rounded px-3 {{ $service->due_amount > 0 ? 'bg-danger bg-opacity-10' : 'bg-success bg-opacity-10' }}">
                    <span class="fw-semibold {{ $service->due_amount > 0 ? 'text-danger' : 'text-success' }}">Balance due</span>
                    <span class="fw-bold {{ $service->due_amount > 0 ? 'text-danger' : 'text-success' }}">৳{{ number_format($service->due_amount, 2) }}</span>
                </div>
                @if($service->due_amount > 0)
                <button type="button" class="btn btn-success btn-sm w-100 mt-3" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                    <i class="fas fa-hand-holding-usd me-1"></i>Collect ৳{{ number_format($service->due_amount, 2) }}
                </button>
                @endif
            </div>
        </div>

        @if($service->returns->count() > 0)
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-undo me-2 text-primary"></i>Service returns</h6>
            </div>
            <div class="p-4">
                @foreach($service->returns as $return)
                <div class="d-flex justify-content-between align-items-center mb-2 p-3 rounded" style="background:#f8fafc;">
                    <div>
                        <a href="{{ route('service-returns.show', $return) }}" class="fw-semibold small text-decoration-none">{{ $return->return_number }}</a>
                        <span class="text-muted small d-block">{{ $return->return_date->format('d M Y') }}</span>
                    </div>
                    <div class="text-end">
                        @if($return->status === 'completed')
                            <span class="badge bg-success small">Completed</span>
                        @elseif($return->status === 'approved')
                            <span class="badge bg-info small">Approved</span>
                        @else
                            <span class="badge bg-warning text-dark small">Pending</span>
                        @endif
                        @if($return->refund_amount > 0)
                        <div class="small text-danger fw-semibold mt-1">-৳{{ number_format($return->refund_amount, 2) }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Danger zone --}}
        <div class="table-card border-danger">
            <div class="table-card-header" style="background:#fef2f2;border-left:3px solid #ef4444;">
                <h6 class="mb-0 fw-semibold text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger zone</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"><i class="fas fa-trash me-1"></i>Delete service order</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Collect Payment Modal --}}
@if($service->due_amount > 0)
<div class="modal fade" id="collectPaymentModal" tabindex="-1" aria-labelledby="collectPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#f0fdf4;border-bottom:1px solid #dcfce7;">
                <h5 class="modal-title fw-bold" id="collectPaymentModalLabel">
                    <i class="fas fa-hand-holding-usd me-2 text-success"></i>Collect Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('services.collect-payment', $service) }}" method="POST" id="collectPaymentForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between mb-4 p-3 rounded" style="background:#fafafa;border:1px solid #f3f4f6;">
                        <div class="text-center flex-grow-1">
                            <div style="font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:600;">Total Cost</div>
                            <div style="font-size:16px;font-weight:800;">৳{{ number_format($service->service_cost, 2) }}</div>
                        </div>
                        <div style="width:1px;background:#e5e7eb;"></div>
                        <div class="text-center flex-grow-1">
                            <div style="font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:600;">Already Paid</div>
                            <div style="font-size:16px;font-weight:800;color:#16a34a;">৳{{ number_format($service->paid_amount, 2) }}</div>
                        </div>
                        <div style="width:1px;background:#e5e7eb;"></div>
                        <div class="text-center flex-grow-1">
                            <div style="font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:600;">Due Amount</div>
                            <div style="font-size:16px;font-weight:800;color:#ef4444;">৳{{ number_format($service->due_amount, 2) }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Amount (BDT) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">৳</span>
                            <input type="number" step="0.01" min="0.01" max="{{ $service->due_amount }}"
                                   class="form-control @error('payment_amount') is-invalid @enderror"
                                   name="payment_amount" id="modal_payment_amount"
                                   value="{{ old('payment_amount', $service->due_amount) }}"
                                   required oninput="updateAfterPayment()">
                            @error('payment_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setPaymentAmount({{ $service->due_amount }})">Full Amount</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setPaymentAmount({{ $service->due_amount / 2 }})">Half</button>
                        </div>
                        <div class="mt-2 p-2 rounded" style="background:#f0fdf4;font-size:13px;" id="afterPaymentPreview">
                            <span class="text-muted">Remaining after payment: </span>
                            <strong class="text-success" id="remainingAfterPayment">৳0.00</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror"
                                name="payment_method" id="modal_payment_method" required onchange="toggleModalBankAccount()">
                            <option value="cash" {{ $service->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="card" {{ $service->payment_method == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="mobile_banking" {{ $service->payment_method == 'mobile_banking' ? 'selected' : '' }}>Mobile Banking</option>
                            <option value="bank_transfer" {{ $service->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cheque" {{ $service->payment_method == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="other">Other</option>
                        </select>
                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3" id="modalBankAccountField" style="display:none;">
                        <label class="form-label fw-semibold">Bank Account</label>
                        <select class="form-select" name="bank_account_id">
                            <option value="">Select Bank Account</option>
                            @foreach(\App\Models\BankAccount::active()->orderBy('account_name')->get() as $ba)
                                <option value="{{ $ba->id }}" {{ $service->bank_account_id == $ba->id ? 'selected' : '' }}>
                                    {{ $ba->account_name }} — {{ $ba->bank_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-2"></i>Confirm Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
const dueAmount = {{ $service->due_amount }};

function updateAfterPayment() {
    const entered = parseFloat(document.getElementById('modal_payment_amount').value) || 0;
    const remaining = Math.max(0, dueAmount - entered);
    document.getElementById('remainingAfterPayment').textContent = '৳' + remaining.toFixed(2);
    const preview = document.getElementById('afterPaymentPreview');
    if (remaining === 0) {
        preview.style.background = '#f0fdf4';
        document.getElementById('remainingAfterPayment').style.color = '#16a34a';
    } else {
        preview.style.background = '#fef2f2';
        document.getElementById('remainingAfterPayment').style.color = '#ef4444';
    }
}

function setPaymentAmount(amount) {
    document.getElementById('modal_payment_amount').value = amount.toFixed(2);
    updateAfterPayment();
}

function toggleModalBankAccount() {
    const method = document.getElementById('modal_payment_method').value;
    const field = document.getElementById('modalBankAccountField');
    const needsBank = ['card','mobile_banking','bank_transfer','cheque'].includes(method);
    field.style.display = needsBank ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('modal_payment_amount')) {
        updateAfterPayment();
        toggleModalBankAccount();
    }
    const shouldOpen = {{ ($errors->has('payment_amount') || $errors->has('payment_method')) ? 'true' : 'false' }};
    const hashOpen = window.location.hash === '#collectPayment';
    if ((shouldOpen || hashOpen) && document.getElementById('collectPaymentModal')) {
        var modal = new bootstrap.Modal(document.getElementById('collectPaymentModal'));
        modal.show();
    }
});
</script>
@endpush
@endsection
