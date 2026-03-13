@extends('layouts.dashboard')

@section('title', 'Purchase Orders')
@section('page-title', 'Purchase Orders')

@section('content')

{{-- Stats --}}
<div class="module-stats mb-3">
    <div class="module-stat-card">
        <div class="msc-label">Total Orders</div>
        <div class="msc-value">{{ $stats['total'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #16a34a;">
        <div class="msc-label">Received</div>
        <div class="msc-value" style="color:#16a34a;">{{ $stats['received'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #f97316;">
        <div class="msc-label">Pending</div>
        <div class="msc-value" style="color:#f97316;">{{ $stats['pending'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #ef4444;">
        <div class="msc-label">Total Due</div>
        <div class="msc-value" style="font-size:16px;color:#ef4444;">৳{{ number_format($stats['total_due'], 0) }}</div>
        <div class="msc-sub">Outstanding to suppliers</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #3b82f6;">
        <div class="msc-label">Total Purchased</div>
        <div class="msc-value" style="font-size:15px;color:#3b82f6;">৳{{ number_format($stats['total_cost'], 0) }}</div>
        <div class="msc-sub">Received orders</div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-shopping-bag me-2"></i>All Purchase Orders</h6>
        @can('create purchases')
        <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Create Purchase Order
        </a>
        @endcan
    </div>

    <div class="filter-wrapper">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="PO number, supplier...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="supplier_id" onchange="this.form.submit()">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ request('supplier_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="draft"     {{ request('status')=='draft'     ?'selected':'' }}>Draft</option>
                        <option value="pending"   {{ request('status')=='pending'   ?'selected':'' }}>Pending</option>
                        <option value="ordered"   {{ request('status')=='ordered'   ?'selected':'' }}>Ordered</option>
                        <option value="partial"   {{ request('status')=='partial'   ?'selected':'' }}>Partial</option>
                        <option value="received"  {{ request('status')=='received'  ?'selected':'' }}>Received</option>
                        <option value="cancelled" {{ request('status')=='cancelled' ?'selected':'' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="payment_status" onchange="this.form.submit()">
                        <option value="">Payment Status</option>
                        <option value="paid"    {{ request('payment_status')=='paid'    ?'selected':'' }}>Paid</option>
                        <option value="partial" {{ request('payment_status')=='partial' ?'selected':'' }}>Partial</option>
                        <option value="unpaid"  {{ request('payment_status')=='unpaid'  ?'selected':'' }}>Unpaid</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary flex-grow-1">Search</button>
                    @if(request()->anyFilled(['search','supplier_id','status','payment_status']))
                    <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Supplier</th>
                    <th>Order Date</th>
                    <th class="text-center">Items</th>
                    <th class="text-end">Total</th>
                    <th>Payment</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                <tr>
                    <td>
                        <strong style="font-size:13px;">{{ $purchase->po_number }}</strong>
                        @if($purchase->received_date)
                        <div style="font-size:11px;color:#16a34a;"><i class="fas fa-check-circle me-1"></i>{{ $purchase->received_date->format('d M Y') }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold" style="font-size:14px;">{{ $purchase->supplier->name }}</div>
                        @if($purchase->supplier->company_name)
                        <div style="font-size:11px;color:#9ca3af;">{{ $purchase->supplier->company_name }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $purchase->order_date->format('d M Y') }}</td>
                    <td class="text-center">
                        <span class="badge" style="background:#eff6ff;color:#1d4ed8;font-weight:700;">{{ $purchase->items()->count() }}</span>
                    </td>
                    <td class="text-end fw-bold">৳{{ number_format($purchase->total_amount, 2) }}</td>
                    <td>
                        @if($purchase->payment_status === 'paid')
                            <span class="badge bg-success">✓ Paid</span>
                        @elseif($purchase->payment_status === 'partial')
                            <span class="badge bg-warning text-dark">Partial</span>
                        @else
                            <span class="badge bg-danger">Unpaid</span>
                        @endif
                        <div style="font-size:11px;color:#6b7280;margin-top:2px;">
                            Paid: ৳{{ number_format($purchase->paid_amount, 2) }}
                            @if($purchase->due_amount > 0)
                            <br><span class="text-danger fw-semibold">Due: ৳{{ number_format($purchase->due_amount, 2) }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-center">
                        @php
                            $statusBadge = [
                                'draft'     => ['bg'=>'#f3f4f6','color'=>'#374151'],
                                'pending'   => ['bg'=>'#fff7ed','color'=>'#c2410c'],
                                'ordered'   => ['bg'=>'#eff6ff','color'=>'#1d4ed8'],
                                'partial'   => ['bg'=>'#fefce8','color'=>'#854d0e'],
                                'received'  => ['bg'=>'#f0fdf4','color'=>'#166534'],
                                'cancelled' => ['bg'=>'#fef2f2','color'=>'#991b1b'],
                            ][$purchase->status] ?? ['bg'=>'#f3f4f6','color'=>'#374151'];
                        @endphp
                        <span style="background:{{ $statusBadge['bg'] }};color:{{ $statusBadge['color'] }};padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;">
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            @can('view purchases')
                            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                            @endcan
                            @can('print invoices')
                            <a href="{{ route('purchases.print', $purchase) }}" class="btn btn-outline-secondary" title="Print" target="_blank"><i class="fas fa-print"></i></a>
                            @endcan
                            @can('edit purchases')
                            @if($purchase->status !== 'received')
                            <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            @endif
                            @endcan
                            @can('receive purchases')
                            @if($purchase->status !== 'received' && $purchase->status !== 'cancelled')
                            <a href="{{ route('purchases.receive', $purchase) }}" class="btn btn-outline-success" title="Receive Items"><i class="fas fa-check"></i></a>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">No purchase orders found.</p>
                        @can('create purchases')
                        <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Create First Order
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($purchases->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">Showing {{ $purchases->firstItem() }}–{{ $purchases->lastItem() }} of {{ $purchases->total() }} orders</small>
        {{ $purchases->links() }}
    </div>
    @endif
</div>
@endsection
