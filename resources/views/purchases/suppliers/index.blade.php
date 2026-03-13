@extends('layouts.dashboard')

@section('title', 'Suppliers')
@section('page-title', 'Suppliers')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-truck me-2"></i>All Suppliers</h6>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Supplier</a>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-6 mb-2"><input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search suppliers..."></div>
            <div class="col-md-3 mb-2"><select class="form-select" name="status" onchange="this.form.submit()"><option value="">All</option><option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option></select></div>
            <div class="col-md-3 mb-2"><button type="submit" class="btn btn-outline-primary w-100">Search</button></div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Name</th><th>Contact</th><th>Purchase Orders</th><th>Total Due</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    @php
                        $totalDue = $supplier->purchaseOrders()->sum('due_amount') ?? 0;
                        $totalPurchases = $supplier->purchaseOrders()->sum('total_amount') ?? 0;
                    @endphp
                    <tr>
                        <td><strong>{{ $supplier->name }}</strong>@if($supplier->company_name)<br><small class="text-muted">{{ $supplier->company_name }}</small>@endif</td>
                        <td>
                            @if($supplier->phone)<div><i class="fas fa-phone"></i> {{ $supplier->phone }}</div>@endif
                            @if($supplier->email)<div><i class="fas fa-envelope"></i> {{ $supplier->email }}</div>@endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $supplier->purchase_orders_count ?? 0 }}</span><br>
                            <small class="text-muted">Total: ৳{{ number_format($totalPurchases, 2) }}</small>
                        </td>
                        <td>
                            @if($totalDue > 0)
                                <strong class="text-danger">৳{{ number_format($totalDue, 2) }}</strong>
                            @else
                                <span class="text-success">৳0.00</span>
                            @endif
                        </td>
                        <td><span class="badge {{ $supplier->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $supplier->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-info" title="View Details"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger" title="Delete"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No suppliers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $suppliers->links() }}
</div>
@endsection

