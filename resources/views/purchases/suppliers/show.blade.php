@extends('layouts.dashboard')

@section('title', 'Supplier Details')
@section('page-title', 'Supplier Details')

@section('content')
<div class="row">
    <!-- Supplier Information -->
    <div class="col-md-12 mb-4">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-truck me-2"></i>{{ $supplier->name }}</h6>
                <div>
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            <div class="p-3">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><th>Name:</th><td>{{ $supplier->name }}</td></tr>
                            @if($supplier->company_name)<tr><th>Company:</th><td>{{ $supplier->company_name }}</td></tr>@endif
                            @if($supplier->email)<tr><th>Email:</th><td>{{ $supplier->email }}</td></tr>@endif
                            @if($supplier->phone)<tr><th>Phone:</th><td>{{ $supplier->phone }}</td></tr>@endif
                            @if($supplier->mobile)<tr><th>Mobile:</th><td>{{ $supplier->mobile }}</td></tr>@endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            @if($supplier->address)<tr><th>Address:</th><td>{{ $supplier->address }}</td></tr>@endif
                            @if($supplier->city)<tr><th>City:</th><td>{{ $supplier->city }}{{ $supplier->country ? ', ' . $supplier->country : '' }}</td></tr>@endif
                            @if($supplier->tax_id)<tr><th>Tax ID:</th><td>{{ $supplier->tax_id }}</td></tr>@endif
                            <tr><th>Status:</th><td><span class="badge {{ $supplier->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $supplier->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Financial Summary Cards -->
    <div class="col-md-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="table-card">
                    <div class="p-3 text-center">
                        <h6 class="text-muted mb-2">Total Orders</h6>
                        <h3 class="mb-0">{{ $summary['total_orders'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="table-card">
                    <div class="p-3 text-center">
                        <h6 class="text-muted mb-2">Total Purchases</h6>
                        <h3 class="mb-0 text-primary">৳{{ number_format($summary['total_purchases'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="table-card">
                    <div class="p-3 text-center">
                        <h6 class="text-muted mb-2">Total Paid</h6>
                        <h3 class="mb-0 text-success">৳{{ number_format($summary['total_paid'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="table-card">
                    <div class="p-3 text-center">
                        <h6 class="text-muted mb-2">Total Due</h6>
                        <h3 class="mb-0 text-danger">৳{{ number_format($summary['total_due'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Status Summary -->
    <div class="col-md-12 mb-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="table-card">
                    <div class="p-3 text-center">
                        <h6 class="text-muted mb-2">Unpaid Orders</h6>
                        <h4 class="mb-0"><span class="badge bg-danger">{{ $summary['unpaid_orders'] }}</span></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="table-card">
                    <div class="p-3 text-center">
                        <h6 class="text-muted mb-2">Partial Payments</h6>
                        <h4 class="mb-0"><span class="badge bg-warning text-dark">{{ $summary['partial_orders'] }}</span></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="table-card">
                    <div class="p-3 text-center">
                        <h6 class="text-muted mb-2">Paid Orders</h6>
                        <h4 class="mb-0"><span class="badge bg-success">{{ $summary['paid_orders'] }}</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transaction History -->
    <div class="col-md-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-list me-2"></i>Purchase Order Transactions</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th>Status</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allPurchases as $purchase)
                            <tr>
                                <td><strong>{{ $purchase->po_number }}</strong></td>
                                <td>{{ $purchase->order_date->format('d M Y') }}</td>
                                <td><strong>৳{{ number_format($purchase->total_amount, 2) }}</strong></td>
                                <td>৳{{ number_format($purchase->paid_amount, 2) }}</td>
                                <td>
                                    @if($purchase->due_amount > 0)
                                        <strong class="text-danger">৳{{ number_format($purchase->due_amount, 2) }}</strong>
                                    @else
                                        <span class="text-success">৳0.00</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $purchase->status === 'received' ? 'bg-success' : ($purchase->status === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                        {{ ucfirst($purchase->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $purchase->payment_status === 'paid' ? 'bg-success' : ($purchase->payment_status === 'partial' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ ucfirst($purchase->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($purchase->payment_status !== 'paid' && $purchase->status === 'received')
                                        <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-sm btn-warning" title="Record Payment">
                                            <i class="fas fa-money-bill"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No purchase orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $allPurchases->links() }}
        </div>
    </div>
</div>
@endsection
