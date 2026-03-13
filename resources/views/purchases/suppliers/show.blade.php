@extends('layouts.dashboard')

@section('title', 'Supplier Details')
@section('page-title', 'Supplier Details')

@section('content')

{{-- Stats --}}
<div class="module-stats mb-3">
    <div class="module-stat-card">
        <div class="msc-label">Total Orders</div>
        <div class="msc-value">{{ $summary['total_orders'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #3b82f6;">
        <div class="msc-label">Total Purchased</div>
        <div class="msc-value" style="font-size:16px;color:#3b82f6;">৳{{ number_format($summary['total_purchases'], 0) }}</div>
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
            <span class="badge bg-danger">{{ $summary['unpaid_orders'] }}</span>
            <span class="badge bg-warning text-dark">{{ $summary['partial_orders'] }}</span>
            <span class="badge bg-success">{{ $summary['paid_orders'] }}</span>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- Supplier Info Card --}}
    <div class="col-md-4">
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-truck me-2"></i>Supplier Profile</h6>
                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
            </div>
            <div class="p-4">
                <div class="text-center mb-4">
                    <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#bfdbfe,#3b82f6);display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:26px;margin:0 auto 12px;">
                        {{ strtoupper(substr($supplier->name, 0, 1)) }}
                    </div>
                    <div class="fw-bold" style="font-size:18px;">{{ $supplier->name }}</div>
                    @if($supplier->company_name)
                    <div style="font-size:12px;color:#9ca3af;">{{ $supplier->company_name }}</div>
                    @endif
                    @if($supplier->is_active)
                        <span class="badge bg-success" style="font-size:11px;">Active</span>
                    @else
                        <span class="badge bg-secondary" style="font-size:11px;">Inactive</span>
                    @endif
                </div>

                <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                    @if($supplier->phone)
                    <tr>
                        <td class="text-muted ps-0" style="width:35%;">Phone</td>
                        <td class="pe-0"><a href="tel:{{ $supplier->phone }}" style="color:inherit;">{{ $supplier->phone }}</a></td>
                    </tr>
                    @endif
                    @if($supplier->mobile)
                    <tr>
                        <td class="text-muted ps-0">Mobile</td>
                        <td class="pe-0"><a href="tel:{{ $supplier->mobile }}" style="color:inherit;">{{ $supplier->mobile }}</a></td>
                    </tr>
                    @endif
                    @if($supplier->email)
                    <tr>
                        <td class="text-muted ps-0">Email</td>
                        <td class="pe-0" style="font-size:12px;">
                            <a href="mailto:{{ $supplier->email }}" style="color:inherit;">{{ $supplier->email }}</a>
                        </td>
                    </tr>
                    @endif
                    @if($supplier->address)
                    <tr>
                        <td class="text-muted ps-0 align-top">Address</td>
                        <td class="pe-0">{{ $supplier->address }}{{ $supplier->city ? ', '.$supplier->city : '' }}</td>
                    </tr>
                    @endif
                    @if($supplier->tax_id)
                    <tr>
                        <td class="text-muted ps-0">Tax ID</td>
                        <td class="pe-0"><code style="font-size:12px;">{{ $supplier->tax_id }}</code></td>
                    </tr>
                    @endif
                    @if($supplier->notes)
                    <tr>
                        <td class="text-muted ps-0 align-top">Notes</td>
                        <td class="pe-0" style="font-size:12px;color:#6b7280;">{{ $supplier->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="p-3 border-top">
                @can('create purchases')
                <a href="{{ route('purchases.create') }}" class="btn btn-primary w-100 mb-2">
                    <i class="fas fa-shopping-cart me-2"></i>New Purchase Order
                </a>
                @endcan
                @if($summary['total_due'] > 0)
                <a href="{{ route('purchases.index', ['supplier_id' => $supplier->id, 'payment_status' => 'unpaid']) }}" class="btn btn-outline-danger w-100">
                    <i class="fas fa-money-bill me-2"></i>View Unpaid Orders ({{ $summary['unpaid_orders'] + $summary['partial_orders'] }})
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Purchase History --}}
    <div class="col-md-8">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-list me-2"></i>Purchase History</h6>
                @can('view purchases')
                <a href="{{ route('purchases.index', ['supplier_id' => $supplier->id]) }}" class="btn btn-sm btn-outline-secondary">
                    View All
                </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Date</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allPurchases as $purchase)
                        <tr>
                            <td>
                                <a href="{{ route('purchases.show', $purchase) }}" class="text-primary fw-semibold" style="font-size:13px;">
                                    {{ $purchase->po_number }}
                                </a>
                            </td>
                            <td style="font-size:13px;">{{ $purchase->order_date->format('d M Y') }}</td>
                            <td class="text-end fw-bold">৳{{ number_format($purchase->total_amount, 2) }}</td>
                            <td class="text-end text-success">৳{{ number_format($purchase->paid_amount, 2) }}</td>
                            <td class="text-end {{ $purchase->due_amount > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                ৳{{ number_format($purchase->due_amount, 2) }}
                            </td>
                            <td class="text-center">
                                @php
                                    $pBadge = ['paid'=>'bg-success','partial'=>'bg-warning text-dark','unpaid'=>'bg-danger'][$purchase->payment_status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $pBadge }}" style="font-size:10px;">{{ ucfirst($purchase->payment_status) }}</span>
                                <span class="badge {{ $purchase->status === 'received' ? 'bg-success' : 'bg-primary' }} ms-1" style="font-size:10px;">{{ ucfirst($purchase->status) }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('purchases.print', $purchase) }}" class="btn btn-outline-secondary" title="Print" target="_blank"><i class="fas fa-print"></i></a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No purchase orders found for this supplier.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($allPurchases->hasPages())
            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
                <small class="text-muted">{{ $allPurchases->total() }} orders total</small>
                {{ $allPurchases->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
