@extends('layouts.dashboard')

@section('title', 'Payments')
@section('page-title', 'Payment Management')

@section('content')

@php
    $sym = $currencySymbol ?? '৳';
    $customerTotal = (float) ($totalCustomerPayments ?? 0);
    $supplierTotal = (float) ($totalSupplierPayments ?? 0);
    $netFlow = $customerTotal - $supplierTotal;
@endphp

{{-- Top summary --}}
<div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-3">
    <div>
        <h5 class="mb-1 fw-bold">Payment Management</h5>
        <p class="text-muted small mb-0">Track customer receipts, supplier payments, and net cash/bank flow.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @can('create payments')
        <a href="{{ route('payments.create', ['type' => 'customer']) }}" class="btn btn-success btn-sm">
            <i class="fas fa-arrow-down me-1"></i>Customer Receipt
        </a>
        <a href="{{ route('payments.create', ['type' => 'supplier']) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-up me-1"></i>Supplier Payment
        </a>
        @endcan
    </div>
</div>

{{-- KPI cards --}}
<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <div class="table-card p-3 border-0 shadow-sm h-100" style="border-left:4px solid #16a34a !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.04em;">Customer receipts</div>
                    <div class="fs-4 fw-bold text-success">{{ $sym }}{{ number_format($customerTotal, 0) }}</div>
                    <div class="text-muted small">Total received from customers</div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:rgba(22,163,74,.12);color:#16a34a;">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="table-card p-3 border-0 shadow-sm h-100" style="border-left:4px solid #3b82f6 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.04em;">Supplier payments</div>
                    <div class="fs-4 fw-bold text-primary">{{ $sym }}{{ number_format($supplierTotal, 0) }}</div>
                    <div class="text-muted small">Total paid to suppliers</div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:rgba(59,130,246,.12);color:#3b82f6;">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="table-card p-3 border-0 shadow-sm h-100" style="border-left:4px solid #8b5cf6 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.04em;">Net flow</div>
                    <div class="fs-4 fw-bold" style="color:#6d28d9;">{{ $sym }}{{ number_format($netFlow, 0) }}</div>
                    <div class="text-muted small">Receipts − Payments</div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:rgba(139,92,246,.12);color:#8b5cf6;">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header bg-light border-0 py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-money-bill-wave me-2 text-success"></i>All payments</h6>
        <div class="small text-muted">
            @if($payments->total() ?? 0)
                {{ $payments->total() }} records
            @endif
        </div>
    </div>

    <div class="filter-wrapper">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4 col-lg-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Payment #, reference, name...">
                    </div>
                </div>
                <div class="col-6 col-md-2 col-lg-2">
                    <select class="form-select form-select-sm" name="type" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="customer" {{ request('type')=='customer'?'selected':'' }}>Customer</option>
                        <option value="supplier" {{ request('type')=='supplier'?'selected':'' }}>Supplier</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 col-lg-2">
                    <select class="form-select form-select-sm" name="payment_method" onchange="this.form.submit()">
                        <option value="">All Methods</option>
                        <option value="cash"           {{ request('payment_method')=='cash'           ?'selected':'' }}>Cash</option>
                        <option value="card"           {{ request('payment_method')=='card'           ?'selected':'' }}>Card</option>
                        <option value="mobile_banking" {{ request('payment_method')=='mobile_banking' ?'selected':'' }}>Mobile Banking</option>
                        <option value="bank_transfer"  {{ request('payment_method')=='bank_transfer'  ?'selected':'' }}>Bank Transfer</option>
                        <option value="cheque"         {{ request('payment_method')=='cheque'         ?'selected':'' }}>Cheque</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 col-lg-2">
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}" placeholder="From">
                </div>
                <div class="col-6 col-md-2 col-lg-2">
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}" placeholder="To">
                </div>
                <div class="col-12 col-md-12 col-lg-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-outline-primary flex-grow-1"><i class="fas fa-search"></i></button>
                    @if(request()->anyFilled(['search','type','payment_method','date_from','date_to']))
                    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Payment #</th>
                    <th class="text-center">Type</th>
                    <th>Invoice / PO</th>
                    <th>Party</th>
                    <th class="text-end">Amount</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>
                        <div class="fw-bold" style="font-size:13px;">{{ $payment->payment_number }}</div>
                        @if($payment->reference_number)
                        <div style="font-size:11px;color:#9ca3af;">Ref: {{ $payment->reference_number }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($payment->payment_type === 'customer')
                            <span class="badge" style="background:#f0fdf4;color:#166534;font-weight:700;">
                                <i class="fas fa-arrow-down me-1"></i>Receipt
                            </span>
                        @else
                            <span class="badge" style="background:#eff6ff;color:#1d4ed8;font-weight:700;">
                                <i class="fas fa-arrow-up me-1"></i>Payment
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($payment->payment_type === 'customer' && $payment->sale)
                            <a href="{{ route('sales.show', $payment->sale) }}" class="text-primary fw-semibold" style="font-size:13px;">
                                {{ $payment->sale->invoice_number }}
                            </a>
                            <div class="small text-muted">Invoice</div>
                        @elseif($payment->payment_type === 'customer' && $payment->service)
                            <a href="{{ route('services.show', $payment->service) }}" class="text-primary fw-semibold" style="font-size:13px;">
                                {{ $payment->service->service_number }}
                            </a>
                            <div class="small text-muted">Service</div>
                        @elseif($payment->payment_type === 'supplier' && $payment->purchase)
                            <a href="{{ route('purchases.show', $payment->purchase) }}" class="text-primary fw-semibold" style="font-size:13px;">
                                {{ $payment->purchase->po_number }}
                            </a>
                            <div class="small text-muted">PO</div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold" style="font-size:14px;">
                            {{ $payment->customer?->name ?? $payment->supplier?->name ?? '—' }}
                        </div>
                    </td>
                    <td class="text-end fw-bold" style="font-size:15px;">
                        <span class="{{ $payment->payment_type==='customer' ? 'text-success' : 'text-primary' }}">
                            {{ $sym }}{{ number_format($payment->amount, 2) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background:#f3f4f6;color:#374151;font-weight:600;font-size:11px;">
                            {{ $payment->payment_method_name }}
                        </span>
                    </td>
                    <td style="font-size:13px;">{{ $payment->payment_date?->format('d M Y') }}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('payments.show', $payment) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('payments.edit', $payment) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-money-bill-wave fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">No payments found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payments->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">Showing {{ $payments->firstItem() }}–{{ $payments->lastItem() }} of {{ $payments->total() }} payments</small>
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endsection
