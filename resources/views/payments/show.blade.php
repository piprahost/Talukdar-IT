@extends('layouts.dashboard')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div class="row g-3 justify-content-center">
    <div class="col-md-8">

        {{-- Amount Banner --}}
        <div class="table-card mb-3 p-4 text-center" style="background:{{ $payment->payment_type === 'customer' ? 'linear-gradient(135deg,#f0fdf4,#dcfce7)' : 'linear-gradient(135deg,#eff6ff,#dbeafe)' }};border-left:5px solid {{ $payment->payment_type === 'customer' ? '#16a34a' : '#3b82f6' }};">
            <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:{{ $payment->payment_type === 'customer' ? '#166534' : '#1d4ed8' }};margin-bottom:8px;">
                @if($payment->payment_type === 'customer')
                    <i class="fas fa-arrow-down me-1"></i>Customer Payment Received
                @else
                    <i class="fas fa-arrow-up me-1"></i>Supplier Payment Made
                @endif
            </div>
            <div style="font-size:40px;font-weight:800;color:{{ $payment->payment_type === 'customer' ? '#16a34a' : '#1d4ed8' }};">
                ৳{{ number_format($payment->amount, 2) }}
            </div>
            <div style="font-size:14px;color:#6b7280;margin-top:4px;">
                {{ $payment->payment_date->format('d M Y') }} · {{ $payment->payment_method_name }}
                @if($payment->reference_number)
                    · Ref: <code>{{ $payment->reference_number }}</code>
                @endif
            </div>
        </div>

        {{-- Details --}}
        <div class="row g-3">
            <div class="col-md-6">
                <div class="table-card h-100">
                    <div class="table-card-header">
                        <h6><i class="fas fa-info-circle me-2"></i>Payment Information</h6>
                    </div>
                    <div class="p-3">
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr>
                                <td class="text-muted ps-0" style="width:45%;">Payment Number</td>
                                <td class="pe-0"><strong>{{ $payment->payment_number }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Type</td>
                                <td class="pe-0">
                                    @if($payment->payment_type === 'customer')
                                        <span class="badge" style="background:#f0fdf4;color:#166534;font-weight:700;">Customer Receipt</span>
                                    @else
                                        <span class="badge" style="background:#eff6ff;color:#1d4ed8;font-weight:700;">Supplier Payment</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Amount</td>
                                <td class="pe-0 fw-bold" style="font-size:16px;">৳{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Date</td>
                                <td class="pe-0">{{ $payment->payment_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Method</td>
                                <td class="pe-0">
                                    <span class="badge" style="background:#f3f4f6;color:#374151;font-weight:600;">{{ $payment->payment_method_name }}</span>
                                </td>
                            </tr>
                            @if($payment->reference_number)
                            <tr>
                                <td class="text-muted ps-0">Reference</td>
                                <td class="pe-0"><code style="font-size:12px;">{{ $payment->reference_number }}</code></td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted ps-0">Created By</td>
                                <td class="pe-0">{{ $payment->creator->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Recorded</td>
                                <td class="pe-0" style="font-size:12px;">{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="table-card h-100">
                    <div class="table-card-header">
                        <h6>
                            @if($payment->payment_type === 'customer')
                                <i class="fas fa-user me-2"></i>Customer / Invoice
                            @else
                                <i class="fas fa-truck me-2"></i>Supplier / PO
                            @endif
                        </h6>
                    </div>
                    <div class="p-3">
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            @if($payment->payment_type === 'customer')
                                @if($payment->customer)
                                <tr>
                                    <td class="text-muted ps-0" style="width:45%;">Customer</td>
                                    <td class="pe-0 fw-semibold">{{ $payment->customer->name }}</td>
                                </tr>
                                @if($payment->customer->phone)
                                <tr>
                                    <td class="text-muted ps-0">Phone</td>
                                    <td class="pe-0"><a href="tel:{{ $payment->customer->phone }}" style="color:inherit;">{{ $payment->customer->phone }}</a></td>
                                </tr>
                                @endif
                                @endif
                                @if($payment->sale)
                                <tr>
                                    <td class="text-muted ps-0">Invoice</td>
                                    <td class="pe-0"><a href="{{ route('sales.show', $payment->sale) }}" class="text-primary fw-semibold">{{ $payment->sale->invoice_number }}</a></td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Invoice Total</td>
                                    <td class="pe-0">৳{{ number_format($payment->sale->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Remaining Due</td>
                                    <td class="pe-0 {{ $payment->sale->due_amount > 0 ? 'text-danger fw-semibold' : 'text-success' }}">
                                        ৳{{ number_format($payment->sale->due_amount, 2) }}
                                    </td>
                                </tr>
                                @endif
                            @else
                                @if($payment->supplier)
                                <tr>
                                    <td class="text-muted ps-0" style="width:45%;">Supplier</td>
                                    <td class="pe-0 fw-semibold">{{ $payment->supplier->name }}</td>
                                </tr>
                                @if($payment->supplier->phone)
                                <tr>
                                    <td class="text-muted ps-0">Phone</td>
                                    <td class="pe-0"><a href="tel:{{ $payment->supplier->phone }}" style="color:inherit;">{{ $payment->supplier->phone }}</a></td>
                                </tr>
                                @endif
                                @endif
                                @if($payment->purchase)
                                <tr>
                                    <td class="text-muted ps-0">PO Number</td>
                                    <td class="pe-0"><a href="{{ route('purchases.show', $payment->purchase) }}" class="text-primary fw-semibold">{{ $payment->purchase->po_number }}</a></td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">PO Total</td>
                                    <td class="pe-0">৳{{ number_format($payment->purchase->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Remaining Due</td>
                                    <td class="pe-0 {{ $payment->purchase->due_amount > 0 ? 'text-danger fw-semibold' : 'text-success' }}">
                                        ৳{{ number_format($payment->purchase->due_amount, 2) }}
                                    </td>
                                </tr>
                                @endif
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($payment->notes)
        <div class="table-card mt-3">
            <div class="table-card-header"><h6><i class="fas fa-sticky-note me-2"></i>Notes</h6></div>
            <div class="p-3"><p class="mb-0 text-muted" style="font-size:14px;">{{ $payment->notes }}</p></div>
        </div>
        @endif

        {{-- Actions --}}
        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('payments.edit', $payment) }}" class="btn btn-outline-warning">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Payments
            </a>
        </div>
    </div>
</div>
@endsection
