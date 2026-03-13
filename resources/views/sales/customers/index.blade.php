@extends('layouts.dashboard')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-users me-2"></i>All Customers</h6>
        <a href="{{ route('customers.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Customer</a>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-6 mb-2"><input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search customers..."></div>
            <div class="col-md-3 mb-2"><select class="form-select" name="status" onchange="this.form.submit()"><option value="">All</option><option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option></select></div>
            <div class="col-md-3 mb-2"><button type="submit" class="btn btn-outline-primary w-100">Search</button></div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Name</th><th>Contact</th><th>Sales</th><th>Total Due</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($customers as $customer)
                    @php
                        $totalDue = $customer->sales()->sum('due_amount') ?? 0;
                        $totalSales = $customer->sales()->sum('total_amount') ?? 0;
                    @endphp
                    <tr>
                        <td><strong>{{ $customer->name }}</strong></td>
                        <td>
                            @if($customer->phone)<div><i class="fas fa-phone"></i> {{ $customer->phone }}</div>@endif
                            @if($customer->email)<div><i class="fas fa-envelope"></i> {{ $customer->email }}</div>@endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $customer->sales_count ?? 0 }}</span><br>
                            <small class="text-muted">Total: ৳{{ number_format($totalSales, 2) }}</small>
                        </td>
                        <td>
                            @if($totalDue > 0)
                                <strong class="text-danger">৳{{ number_format($totalDue, 2) }}</strong>
                            @else
                                <span class="text-success">৳0.00</span>
                            @endif
                        </td>
                        <td><span class="badge {{ $customer->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $customer->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-info" title="View Details"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger" title="Delete"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $customers->links() }}
</div>
@endsection

