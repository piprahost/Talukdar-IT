@extends('layouts.dashboard')

@section('title', 'Purchase Orders')
@section('page-title', 'Purchase Orders')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-shopping-bag me-2"></i>All Purchase Orders</h6>
        @can('create purchases')
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Purchase Order
        </a>
        @endcan
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-2"><input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search PO number, supplier..."></div>
            <div class="col-md-3 mb-2"><select class="form-select" name="supplier_id" onchange="this.form.submit()"><option value="">All Suppliers</option>@foreach($suppliers as $s)<option value="{{ $s->id }}" {{ request('supplier_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach</select></div>
            <div class="col-md-3 mb-2"><select class="form-select" name="status" onchange="this.form.submit()"><option value="">All Status</option><option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option><option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option><option value="ordered" {{ request('status')=='ordered'?'selected':'' }}>Ordered</option><option value="partial" {{ request('status')=='partial'?'selected':'' }}>Partial</option><option value="received" {{ request('status')=='received'?'selected':'' }}>Received</option></select></div>
            <div class="col-md-2 mb-2"><button type="submit" class="btn btn-outline-primary w-100">Search</button></div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Supplier</th>
                    <th>Order Date</th>
                    <th>Items</th>
                    <th>Total Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                    <tr>
                        <td><strong>{{ $purchase->po_number }}</strong></td>
                        <td>{{ $purchase->supplier->name }}</td>
                        <td>{{ $purchase->order_date->format('d M Y') }}</td>
                        <td><span class="badge bg-info">{{ $purchase->items()->count() }}</span></td>
                        <td><strong>৳{{ number_format($purchase->total_amount, 2) }}</strong></td>
                        <td>
                            <small>Paid: ৳{{ number_format($purchase->paid_amount, 2) }}</small><br>
                            <small class="text-danger">Due: ৳{{ number_format($purchase->due_amount, 2) }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $purchase->status === 'received' ? 'bg-success' : ($purchase->status === 'partial' ? 'bg-warning text-dark' : ($purchase->status === 'cancelled' ? 'bg-danger' : 'bg-primary')) }}">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @can('view purchases')
                                <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-info" title="View"><i class="fas fa-eye"></i></a>
                                @endcan
                                @can('edit purchases')
                                @if($purchase->status !== 'received')
                                    <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                @endif
                                @endcan
                                @can('receive purchases')
                                @if($purchase->status !== 'received')
                                    <a href="{{ route('purchases.receive', $purchase) }}" class="btn btn-success" title="Receive Items"><i class="fas fa-check"></i></a>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No purchase orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($purchases->hasPages())
        <div class="d-flex justify-content-center mt-4 mb-3">
            {{ $purchases->links() }}
        </div>
    @endif
</div>
@endsection

