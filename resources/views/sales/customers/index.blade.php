@extends('layouts.dashboard')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')

{{-- Stats --}}
<div class="module-stats mb-3">
    <div class="module-stat-card">
        <div class="msc-label">Total Customers</div>
        <div class="msc-value">{{ $stats['total'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #16a34a;">
        <div class="msc-label">Active</div>
        <div class="msc-value" style="color:#16a34a;">{{ $stats['active'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #3b82f6;">
        <div class="msc-label">Total Sales</div>
        <div class="msc-value" style="font-size:15px;color:#3b82f6;">৳{{ number_format($stats['total_sales'], 0) }}</div>
        <div class="msc-sub">Completed orders</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #ef4444;">
        <div class="msc-label">Total Dues</div>
        <div class="msc-value" style="font-size:15px;color:#ef4444;">৳{{ number_format($stats['total_due'], 0) }}</div>
        <div class="msc-sub">Outstanding from customers</div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-users me-2"></i>All Customers</h6>
        @can('create customers')
        <a href="{{ route('customers.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Add Customer
        </a>
        @endcan
    </div>

    <div class="filter-wrapper">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, email...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                        <option value="">All Customers</option>
                        <option value="active"   {{ request('status')=='active'   ?'selected':'' }}>Active Only</option>
                        <option value="inactive" {{ request('status')=='inactive' ?'selected':'' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary flex-grow-1">Search</button>
                    @if(request()->anyFilled(['search','status']))
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th class="text-center">Orders</th>
                    <th class="text-end">Total Purchased</th>
                    <th class="text-end">Due Amount</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#bbf7d0,#16a34a);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:14px;flex-shrink:0;">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:14px;">{{ $customer->name }}</div>
                                @if($customer->city)
                                <div style="font-size:11px;color:#9ca3af;"><i class="fas fa-map-marker-alt me-1"></i>{{ $customer->city }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($customer->phone)
                        <a href="tel:{{ $customer->phone }}" style="color:inherit;text-decoration:none;font-size:13px;">
                            <i class="fas fa-phone fa-xs me-1 text-muted"></i>{{ $customer->phone }}
                        </a>
                        @endif
                        @if($customer->email)
                        <div style="font-size:12px;color:#9ca3af;">
                            <i class="fas fa-envelope fa-xs me-1"></i>{{ $customer->email }}
                        </div>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:#eff6ff;color:#1d4ed8;font-weight:700;font-size:13px;">
                            {{ $customer->sales_count }}
                        </span>
                    </td>
                    <td class="text-end fw-semibold">
                        ৳{{ number_format($customer->sales_sum_total_amount ?? 0, 2) }}
                    </td>
                    <td class="text-end">
                        @php $due = $customer->sales_sum_due_amount ?? 0; @endphp
                        @if($due > 0)
                            <strong class="text-danger">৳{{ number_format($due, 2) }}</strong>
                        @else
                            <span class="text-success">৳0.00</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($customer->is_active)
                            <span class="badge bg-success" style="font-size:11px;">Active</span>
                        @else
                            <span class="badge bg-secondary" style="font-size:11px;">Inactive</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            @if($due > 0)
                            <a href="{{ route('sales.index', ['customer_id'=>$customer->id,'payment_status'=>'unpaid']) }}" class="btn btn-outline-danger" title="View Dues"><i class="fas fa-money-bill-wave"></i></a>
                            @endif
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete {{ addslashes($customer->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">No customers found.</p>
                        @can('create customers')
                        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add First Customer
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">Showing {{ $customers->firstItem() }}–{{ $customers->lastItem() }} of {{ $customers->total() }} customers</small>
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection
