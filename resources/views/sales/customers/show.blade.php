@extends('layouts.dashboard')

@section('title', 'Customer Details')
@section('page-title', 'Customer Details')

@section('content')

{{-- Stats --}}
<div class="module-stats mb-3">
    <div class="module-stat-card">
        <div class="msc-label">Total Orders</div>
        <div class="msc-value">{{ $summary['total_sales'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #3b82f6;">
        <div class="msc-label">Total Amount</div>
        <div class="msc-value" style="font-size:16px;color:#3b82f6;">৳{{ number_format($summary['total_amount'], 0) }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #16a34a;">
        <div class="msc-label">Total Paid</div>
        <div class="msc-value" style="font-size:16px;color:#16a34a;">৳{{ number_format($summary['total_paid'], 0) }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid {{ $summary['total_due'] > 0 ? '#ef4444' : '#16a34a' }};">
        <div class="msc-label">Outstanding Due</div>
        <div class="msc-value" style="font-size:16px;color:{{ $summary['total_due'] > 0 ? '#ef4444' : '#16a34a' }};">৳{{ number_format($summary['total_due'], 0) }}</div>
    </div>
    <div class="module-stat-card">
        <div class="msc-label">Unpaid / Partial / Paid</div>
        <div class="d-flex gap-2 mt-1">
            <span class="badge bg-danger">{{ $summary['unpaid_sales'] }}</span>
            <span class="badge bg-warning text-dark">{{ $summary['partial_sales'] }}</span>
            <span class="badge bg-success">{{ $summary['paid_sales'] }}</span>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- Customer Info Card --}}
    <div class="col-md-4">
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-user me-2"></i>Customer Profile</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                </div>
            </div>
            <div class="p-4">
                <div class="text-center mb-4">
                    <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#bbf7d0,#16a34a);display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:26px;margin:0 auto 12px;">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                    <div class="fw-bold" style="font-size:18px;">{{ $customer->name }}</div>
                    @if($customer->is_active)
                        <span class="badge bg-success" style="font-size:11px;">Active</span>
                    @else
                        <span class="badge bg-secondary" style="font-size:11px;">Inactive</span>
                    @endif
                </div>

                <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                    @if($customer->phone)
                    <tr>
                        <td class="text-muted ps-0" style="width:35%;">Phone</td>
                        <td class="pe-0"><a href="tel:{{ $customer->phone }}" style="color:inherit;">{{ $customer->phone }}</a></td>
                    </tr>
                    @endif
                    @if($customer->mobile)
                    <tr>
                        <td class="text-muted ps-0">Mobile</td>
                        <td class="pe-0"><a href="tel:{{ $customer->mobile }}" style="color:inherit;">{{ $customer->mobile }}</a></td>
                    </tr>
                    @endif
                    @if($customer->email)
                    <tr>
                        <td class="text-muted ps-0">Email</td>
                        <td class="pe-0" style="font-size:12px;">{{ $customer->email }}</td>
                    </tr>
                    @endif
                    @if($customer->address)
                    <tr>
                        <td class="text-muted ps-0 align-top">Address</td>
                        <td class="pe-0">{{ $customer->address }}{{ $customer->city ? ', '.$customer->city : '' }}</td>
                    </tr>
                    @endif
                    @if($customer->tax_id)
                    <tr>
                        <td class="text-muted ps-0">Tax ID</td>
                        <td class="pe-0"><code style="font-size:12px;">{{ $customer->tax_id }}</code></td>
                    </tr>
                    @endif
                    @if($customer->notes)
                    <tr>
                        <td class="text-muted ps-0 align-top">Notes</td>
                        <td class="pe-0" style="font-size:12px;color:#6b7280;">{{ $customer->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="p-3 border-top">
                @can('create sales')
                <a href="{{ route('sales.create') }}" class="btn btn-primary w-100 mb-2">
                    <i class="fas fa-file-invoice-dollar me-2"></i>New Sale / Invoice
                </a>
                @endcan
                <a href="{{ route('sales.index', ['customer_id' => $customer->id, 'payment_status' => 'unpaid']) }}" class="btn btn-outline-danger w-100">
                    <i class="fas fa-money-bill me-2"></i>View Unpaid Invoices ({{ $summary['unpaid_sales'] + $summary['partial_sales'] }})
                </a>
            </div>
        </div>
    </div>

    {{-- Transaction History --}}
    <div class="col-md-8">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-list me-2"></i>Sales History</h6>
                @can('view sales')
                <a href="{{ route('sales.index', ['customer_id' => $customer->id]) }}" class="btn btn-sm btn-outline-secondary">
                    View All
                </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allSales as $sale)
                        <tr>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}" class="text-primary fw-semibold" style="font-size:13px;">
                                    {{ $sale->invoice_number }}
                                </a>
                            </td>
                            <td style="font-size:13px;">{{ $sale->sale_date->format('d M Y') }}</td>
                            <td class="text-end fw-bold">৳{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="text-end text-success">৳{{ number_format($sale->paid_amount, 2) }}</td>
                            <td class="text-end {{ $sale->due_amount > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                ৳{{ number_format($sale->due_amount, 2) }}
                            </td>
                            <td class="text-center">
                                @php
                                    $pBadge = ['paid'=>'bg-success','partial'=>'bg-warning text-dark','unpaid'=>'bg-danger'][$sale->payment_status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $pBadge }}" style="font-size:10px;">{{ ucfirst($sale->payment_status) }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('sales.print', $sale) }}" class="btn btn-outline-secondary" title="Print" target="_blank"><i class="fas fa-print"></i></a>
                                    @if($sale->status === 'completed' && $sale->due_amount > 0)
                                    <a href="{{ route('sales.show', $sale) }}#collectPayment" class="btn btn-success" title="Collect Payment"><i class="fas fa-hand-holding-usd"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No sales found for this customer.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($allSales->hasPages())
            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
                <small class="text-muted">{{ $allSales->total() }} invoices total</small>
                {{ $allSales->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
