@extends('layouts.dashboard')

@section('title', 'Service Details')
@section('page-title', 'Service Order Details')

@section('content')
<div class="service-orders-wrap">
    @php
        $statusColors = [
            'pending'     => ['bg'=>'warning','color'=>'text-dark'],
            'in_progress' => ['bg'=>'info','color'=>'text-white'],
            'completed'   => ['bg'=>'success','color'=>'text-white'],
            'delivered'   => ['bg'=>'primary','color'=>'text-white'],
            'cancelled'   => ['bg'=>'danger','color'=>'text-white'],
        ];
        $sc = $statusColors[$service->status] ?? ['bg'=>'secondary','color'=>'text-white'];
        $statusLabels = ['pending'=>'Pending','in_progress'=>'In Progress','completed'=>'Completed','delivered'=>'Delivered','cancelled'=>'Cancelled'];
    @endphp
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h5 class="mb-1 fw-bold">Service order <code class="bg-light px-2 py-1 rounded">{{ $service->service_number }}</code></h5>
            <p class="text-muted small mb-0">Received {{ $service->receive_date->format('d M Y') }} · <span class="badge bg-{{ $sc['bg'] }} {{ $sc['color'] }}">{{ $statusLabels[$service->status] ?? ucfirst($service->status) }}</span></p>
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

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="table-card p-3 h-100 border-start border-3 border-secondary">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Service cost</div>
                <div class="fw-bold fs-5">৳{{ number_format($service->service_cost, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="table-card p-3 h-100 border-start border-3 border-success">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Paid</div>
                <div class="fw-bold fs-5 text-success">৳{{ number_format($service->paid_amount, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="table-card p-3 h-100 border-start border-3 {{ $service->due_amount > 0 ? 'border-danger' : 'border-success' }}">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Due</div>
                <div class="fw-bold fs-5 {{ $service->due_amount > 0 ? 'text-danger' : 'text-success' }}">৳{{ number_format($service->due_amount, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="table-card p-3 h-100">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Payment status</div>
                @if($service->payment_status === 'fully_paid')
                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Fully Paid</span>
                @elseif($service->payment_status === 'partial')
                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Partial</span>
                @else
                    <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Unpaid</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="table-card h-100">
            <div class="table-card-header bg-light border-0 py-3">
                <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-laptop-medical me-2 text-primary"></i>Product & Customer</h6>
            </div>
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h6 class="small text-uppercase text-muted fw-semibold mb-2"><i class="fas fa-box me-1"></i>Product</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted ps-0" style="width:38%;">Product</td>
                                <td class="fw-semibold pe-0">{{ $service->product_name }}</td>
                            </tr>
                            @if($service->serial_number)
                            <tr>
                                <td class="text-muted ps-0">Serial</td>
                                <td class="pe-0"><code class="bg-light px-2 py-1 rounded small">{{ $service->serial_number }}</code></td>
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
                                <td class="text-muted ps-0 align-top">Technician</td>
                                <td class="pe-0" style="white-space:pre-line;">{{ $service->service_notes }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h6 class="small text-uppercase text-muted fw-semibold mb-2"><i class="fas fa-user me-1"></i>Customer</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted ps-0" style="width:38%;">Name</td>
                                <td class="fw-semibold pe-0">{{ $service->customer_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Phone</td>
                                <td class="pe-0"><a href="tel:{{ $service->customer_phone }}" class="text-decoration-none"><i class="fas fa-phone fa-xs me-1 text-muted"></i>{{ $service->customer_phone }}</a></td>
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
                    <div class="col-md-6 mb-3">
                        <h6 class="small text-uppercase text-muted fw-semibold mb-2"><i class="fas fa-calendar me-1"></i>Dates</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted ps-0" style="width:45%;">Received</td>
                                <td class="fw-semibold pe-0">{{ $service->receive_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Delivery</td>
                                <td class="pe-0">
                                    @if($service->delivery_date)
                                        @php $overdue = $service->delivery_date->isPast() && !in_array($service->status, ['delivered','cancelled']); @endphp
                                        <span class="{{ $overdue ? 'text-danger fw-semibold' : '' }}">{{ $service->delivery_date->format('d M Y') }}@if($overdue) <small><i class="fas fa-exclamation-circle ms-1"></i>Overdue</small>@endif</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Created</td>
                                <td class="pe-0">{{ $service->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="small text-uppercase text-muted fw-semibold mb-2"><i class="fas fa-credit-card me-1"></i>Payment</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted ps-0" style="width:45%;">Method</td>
                                <td class="pe-0"><span class="badge bg-light text-dark fw-semibold">{{ $service->payment_method_label }}</span></td>
                            </tr>
                            @if($service->bankAccount)
                            <tr>
                                <td class="text-muted ps-0">Bank</td>
                                <td class="pe-0">{{ $service->bankAccount->account_name }} — {{ $service->bankAccount->bank_name }}</td>
                            </tr>
                            @endif
                            @if($service->created_by)
                            <tr>
                                <td class="text-muted ps-0">Created by</td>
                                <td class="pe-0">{{ $service->creator->name ?? 'N/A' }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                @if($service->internal_notes)
                <hr class="my-3">
                <h6 class="small text-uppercase text-muted fw-semibold mb-2"><i class="fas fa-sticky-note me-1"></i>Internal notes</h6>
                <p class="text-muted mb-0 small" style="white-space:pre-line;">{{ $service->internal_notes }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="table-card mb-4">
            <div class="table-card-header bg-light border-0 py-3">
                <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-receipt me-2 text-primary"></i>Invoice summary</h6>
            </div>
            <div class="p-4 pt-3">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Service cost</span>
                    <span class="fw-semibold">৳{{ number_format($service->service_cost, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Paid</span>
                    <span class="fw-semibold text-success">৳{{ number_format($service->paid_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between py-3 mt-2 rounded {{ $service->due_amount > 0 ? 'bg-danger bg-opacity-10' : 'bg-success bg-opacity-10' }}">
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
        <div class="table-card mb-4">
            <div class="table-card-header bg-light border-0 py-3">
                <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-undo me-2 text-primary"></i>Service returns</h6>
            </div>
            <div class="p-4 pt-3">
                @foreach($service->returns as $return)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded bg-light">
                    <div>
                        <span class="fw-semibold small">{{ $return->return_number }}</span>
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
                        <div class="small text-danger fw-semibold">-৳{{ number_format($return->refund_amount, 2) }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="table-card">
            <div class="table-card-header bg-light border-0 py-3">
                <h6 class="mb-0 fw-semibold text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger zone</h6>
            </div>
            <div class="p-4 pt-3">
                <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"><i class="fas fa-trash me-1"></i>Delete service order</button>
                </form>
            </div>
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
