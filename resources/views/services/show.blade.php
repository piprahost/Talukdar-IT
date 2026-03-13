@extends('layouts.dashboard')

@section('title', 'Service Details')
@section('page-title', 'Service Order Details')

@section('content')
<div class="row g-3">

    {{-- ── Top Action Bar ── --}}
    <div class="col-12">
        <div class="table-card p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-muted" style="font-size:13px;">
                    <i class="fas fa-hashtag me-1"></i>{{ $service->service_number }}
                </span>
                @php
                    $statusColors = [
                        'pending'     => ['bg'=>'#fff7ed','color'=>'#c2410c','dot'=>'#f97316'],
                        'in_progress' => ['bg'=>'#eff6ff','color'=>'#1d4ed8','dot'=>'#3b82f6'],
                        'completed'   => ['bg'=>'#f0fdf4','color'=>'#166534','dot'=>'#22c55e'],
                        'delivered'   => ['bg'=>'#f5f3ff','color'=>'#5b21b6','dot'=>'#8b5cf6'],
                        'cancelled'   => ['bg'=>'#fef2f2','color'=>'#991b1b','dot'=>'#ef4444'],
                    ];
                    $sc = $statusColors[$service->status] ?? ['bg'=>'#f3f4f6','color'=>'#374151','dot'=>'#9ca3af'];
                    $statusLabels = ['pending'=>'Pending','in_progress'=>'In Progress','completed'=>'Completed','delivered'=>'Delivered','cancelled'=>'Cancelled'];
                @endphp
                <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;">
                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $sc['dot'] }};display:inline-block;"></span>
                    {{ $statusLabels[$service->status] ?? ucfirst($service->status) }}
                </span>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if(in_array($service->status, ['completed', 'delivered']))
                    <a href="{{ route('service-returns.create', ['service_id' => $service->id]) }}" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-undo me-1"></i>Return Service
                    </a>
                @endif
                @if($service->due_amount > 0)
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                        <i class="fas fa-hand-holding-usd me-1"></i>Collect Payment
                    </button>
                @endif
                <a href="{{ route('services.print', $service) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                    <i class="fas fa-print me-1"></i>Print Memo
                </a>
                <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>
    </div>

    {{-- ── Payment Summary Cards ── --}}
    <div class="col-12">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Service Cost</div>
                    <div style="font-size:22px;font-weight:800;color:#111;">৳{{ number_format($service->service_cost, 2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100" style="border-left:3px solid #16a34a;">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Paid Amount</div>
                    <div style="font-size:22px;font-weight:800;color:#16a34a;">৳{{ number_format($service->paid_amount, 2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100" style="border-left:3px solid {{ $service->due_amount > 0 ? '#ef4444' : '#16a34a' }};">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Due Amount</div>
                    <div style="font-size:22px;font-weight:800;color:{{ $service->due_amount > 0 ? '#ef4444' : '#16a34a' }};">৳{{ number_format($service->due_amount, 2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Payment Status</div>
                    @if($service->payment_status === 'fully_paid')
                        <span class="badge bg-success" style="font-size:13px;padding:7px 14px;">
                            <i class="fas fa-check-circle me-1"></i>Fully Paid
                        </span>
                    @elseif($service->payment_status === 'partial')
                        <span class="badge bg-warning text-dark" style="font-size:13px;padding:7px 14px;">
                            <i class="fas fa-clock me-1"></i>Partial
                        </span>
                    @else
                        <span class="badge bg-danger" style="font-size:13px;padding:7px 14px;">
                            <i class="fas fa-times-circle me-1"></i>Unpaid
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main Detail Card ── --}}
    <div class="col-md-8">
        <div class="table-card h-100">
            <div class="table-card-header">
                <h6><i class="fas fa-laptop-medical me-2"></i>Product & Customer Details</h6>
            </div>
            <div class="p-4">
                <div class="row">
                    {{-- Product Info --}}
                    <div class="col-md-6 mb-4">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:12px;">
                            <i class="fas fa-box me-1"></i>Product Information
                        </h6>
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr>
                                <td class="text-muted ps-0" style="width:40%;white-space:nowrap;">Product</td>
                                <td class="fw-600 pe-0">{{ $service->product_name }}</td>
                            </tr>
                            @if($service->serial_number)
                            <tr>
                                <td class="text-muted ps-0">Serial No.</td>
                                <td class="pe-0"><code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:12px;">{{ $service->serial_number }}</code></td>
                            </tr>
                            @endif
                            @if($service->problem_notes)
                            <tr>
                                <td class="text-muted ps-0 align-top">Problem</td>
                                <td class="pe-0" style="white-space:pre-line;">{{ $service->problem_notes }}</td>
                            </tr>
                            @endif
                            @if($service->service_notes)
                            <tr>
                                <td class="text-muted ps-0 align-top">Technician Notes</td>
                                <td class="pe-0" style="white-space:pre-line;">{{ $service->service_notes }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>

                    {{-- Customer Info --}}
                    <div class="col-md-6 mb-4">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:12px;">
                            <i class="fas fa-user me-1"></i>Customer Information
                        </h6>
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr>
                                <td class="text-muted ps-0" style="width:40%;white-space:nowrap;">Name</td>
                                <td class="fw-semibold pe-0">{{ $service->customer_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Phone</td>
                                <td class="pe-0">
                                    <a href="tel:{{ $service->customer_phone }}" style="color:inherit;text-decoration:none;">
                                        <i class="fas fa-phone fa-xs me-1 text-muted"></i>{{ $service->customer_phone }}
                                    </a>
                                </td>
                            </tr>
                            @if($service->customer_address)
                            <tr>
                                <td class="text-muted ps-0 align-top">Address</td>
                                <td class="pe-0">{{ $service->customer_address }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row">
                    {{-- Dates --}}
                    <div class="col-md-6 mb-3">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:12px;">
                            <i class="fas fa-calendar me-1"></i>Dates
                        </h6>
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr>
                                <td class="text-muted ps-0" style="width:45%;">Received</td>
                                <td class="fw-semibold pe-0">{{ $service->receive_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Expected Delivery</td>
                                <td class="pe-0">
                                    @if($service->delivery_date)
                                        @php $overdue = $service->delivery_date->isPast() && !in_array($service->status, ['delivered','cancelled']); @endphp
                                        <span class="{{ $overdue ? 'text-danger fw-semibold' : '' }}">
                                            {{ $service->delivery_date->format('d M Y') }}
                                            @if($overdue) <small><i class="fas fa-exclamation-circle ms-1"></i>Overdue</small> @endif
                                        </span>
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Created</td>
                                <td class="pe-0">{{ $service->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- Payment Details --}}
                    <div class="col-md-6 mb-3">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:12px;">
                            <i class="fas fa-credit-card me-1"></i>Payment Details
                        </h6>
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr>
                                <td class="text-muted ps-0" style="width:45%;">Method</td>
                                <td class="pe-0">
                                    <span class="badge" style="background:#f3f4f6;color:#374151;font-weight:600;">
                                        {{ $service->payment_method_label }}
                                    </span>
                                </td>
                            </tr>
                            @if($service->bankAccount)
                            <tr>
                                <td class="text-muted ps-0">Bank Account</td>
                                <td class="pe-0">{{ $service->bankAccount->account_name }} — {{ $service->bankAccount->bank_name }}</td>
                            </tr>
                            @endif
                            @if($service->created_by)
                            <tr>
                                <td class="text-muted ps-0">Created By</td>
                                <td class="pe-0">{{ $service->creator->name ?? 'N/A' }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($service->internal_notes)
                <hr class="my-3">
                <div>
                    <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:8px;">
                        <i class="fas fa-sticky-note me-1"></i>Internal Notes
                    </h6>
                    <p class="text-muted mb-0" style="font-size:14px;white-space:pre-line;">{{ $service->internal_notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Sidebar: Payment History & Actions ── --}}
    <div class="col-md-4">
        {{-- Payment Breakdown --}}
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-receipt me-2"></i>Invoice Summary</h6>
            </div>
            <div class="p-3">
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:13px;color:#6b7280;">Service Cost</span>
                    <span style="font-size:14px;font-weight:700;">৳{{ number_format($service->service_cost, 2) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:13px;color:#6b7280;">Paid</span>
                    <span style="font-size:14px;font-weight:700;color:#16a34a;">৳{{ number_format($service->paid_amount, 2) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:12px 0;background:{{ $service->due_amount > 0 ? '#fef2f2' : '#f0fdf4' }};margin:0 -12px;padding-left:12px;padding-right:12px;border-radius:0 0 8px 8px;">
                    <span style="font-size:13px;font-weight:700;color:{{ $service->due_amount > 0 ? '#991b1b' : '#166534' }};">Balance Due</span>
                    <span style="font-size:16px;font-weight:800;color:{{ $service->due_amount > 0 ? '#ef4444' : '#16a34a' }};">৳{{ number_format($service->due_amount, 2) }}</span>
                </div>

                @if($service->due_amount > 0)
                <div class="mt-3">
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                        <i class="fas fa-hand-holding-usd me-2"></i>Collect ৳{{ number_format($service->due_amount, 2) }} Due
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- Service Returns --}}
        @if($service->returns->count() > 0)
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-undo me-2"></i>Service Returns</h6>
            </div>
            <div class="p-3">
                @foreach($service->returns as $return)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background:#f9fafb;border-radius:8px;">
                    <div>
                        <div style="font-size:12px;font-weight:700;">{{ $return->return_number }}</div>
                        <div style="font-size:11px;color:#6b7280;">{{ $return->return_date->format('d M Y') }}</div>
                    </div>
                    <div class="text-end">
                        <div>
                            @if($return->status === 'completed')
                                <span class="badge bg-success" style="font-size:10px;">Completed</span>
                            @elseif($return->status === 'approved')
                                <span class="badge bg-info" style="font-size:10px;">Approved</span>
                            @else
                                <span class="badge bg-warning text-dark" style="font-size:10px;">Pending</span>
                            @endif
                        </div>
                        @if($return->refund_amount > 0)
                        <div style="font-size:11px;color:#ef4444;font-weight:600;">-৳{{ number_format($return->refund_amount, 2) }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Danger Zone --}}
        <div class="table-card">
            <div class="table-card-header">
                <h6 class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h6>
            </div>
            <div class="p-3">
                <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="fas fa-trash me-2"></i>Delete Service Order
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

{{-- ── Collect Payment Modal ── --}}
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

                    {{-- Balance info strip --}}
                    <div class="d-flex justify-content-between mb-4 p-3" style="background:#fafafa;border-radius:10px;border:1px solid #f3f4f6;">
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
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setPaymentAmount({{ $service->due_amount }})">
                                Full Amount
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setPaymentAmount({{ $service->due_amount / 2 }})">
                                Half
                            </button>
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
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Confirm Payment
                    </button>
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
    updateAfterPayment();
    toggleModalBankAccount();

    // Auto-open modal on validation error or when navigated via #collectPayment hash
    const shouldOpen = {{ ($errors->has('payment_amount') || $errors->has('payment_method')) ? 'true' : 'false' }};
    const hashOpen   = window.location.hash === '#collectPayment';
    if ((shouldOpen || hashOpen) && document.getElementById('collectPaymentModal')) {
        var modal = new bootstrap.Modal(document.getElementById('collectPaymentModal'));
        modal.show();
    }
});
</script>
@endpush
@endsection
