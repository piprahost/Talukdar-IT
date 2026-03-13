@extends('layouts.dashboard')

@section('title', 'Sales / Invoices')
@section('page-title', 'Sales / Invoices')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-file-invoice-dollar me-2"></i>All Sales / Invoices</h6>
        @can('create sales')
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Sale / Invoice
        </a>
        @endcan
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3 mb-2"><input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search invoice, customer..."></div>
            <div class="col-md-2 mb-2"><select class="form-select" name="customer_id" onchange="this.form.submit()"><option value="">All Customers</option>@foreach($customers as $c)<option value="{{ $c->id }}" {{ request('customer_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select></div>
            <div class="col-md-2 mb-2"><select class="form-select" name="status" onchange="this.form.submit()"><option value="">All Status</option><option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option><option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option><option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option></select></div>
            <div class="col-md-2 mb-2"><select class="form-select" name="payment_status" onchange="this.form.submit()"><option value="">Payment Status</option><option value="unpaid" {{ request('payment_status')=='unpaid'?'selected':'' }}>Unpaid</option><option value="partial" {{ request('payment_status')=='partial'?'selected':'' }}>Partial</option><option value="paid" {{ request('payment_status')=='paid'?'selected':'' }}>Paid</option></select></div>
            <div class="col-md-3 mb-2"><button type="submit" class="btn btn-outline-primary w-100">Search</button></div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td><strong>{{ $sale->invoice_number }}</strong></td>
                        <td>{{ $sale->customer ? $sale->customer->name : ($sale->customer_name ?? 'Walk-in') }}</td>
                        <td>{{ $sale->sale_date->format('d M Y') }}</td>
                        <td><span class="badge bg-info">{{ $sale->items()->count() }}</span></td>
                        <td><strong>৳{{ number_format($sale->total_amount, 2) }}</strong></td>
                        <td>
                            <small>Paid: ৳{{ number_format($sale->paid_amount, 2) }}</small><br>
                            <small class="text-danger">Due: ৳{{ number_format($sale->due_amount, 2) }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $sale->status === 'completed' ? 'bg-success' : ($sale->status === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-info" title="View"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('sales.print', $sale) }}" class="btn btn-secondary" title="Print" target="_blank"><i class="fas fa-print"></i></a>
                                @if($sale->status !== 'completed')
                                    <a href="{{ route('sales.edit', $sale) }}" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No sales found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($sales->hasPages())
        <div class="d-flex justify-content-center mt-4 mb-3">
            {{ $sales->links() }}
        </div>
    @endif
</div>
@endsection

