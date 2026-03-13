@extends('layouts.dashboard')

@section('title', 'Customer Details')
@section('page-title', 'Customer Details')

@section('content')
<div class="row">
    <!-- Customer Information -->
    <div class="col-md-12 mb-4">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-users me-2"></i>{{ $customer->name }}</h6>
                <div>
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            <div class="p-3">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><th>Name:</th><td>{{ $customer->name }}</td></tr>
                            @if($customer->email)<tr><th>Email:</th><td>{{ $customer->email }}</td></tr>@endif
                            @if($customer->phone)<tr><th>Phone:</th><td>{{ $customer->phone }}</td></tr>@endif
                            @if($customer->mobile)<tr><th>Mobile:</th><td>{{ $customer->mobile }}</td></tr>@endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            @if($customer->address)<tr><th>Address:</th><td>{{ $customer->address }}</td></tr>@endif
                            @if($customer->city)<tr><th>City:</th><td>{{ $customer->city }}{{ $customer->country ? ', ' . $customer->country : '' }}</td></tr>@endif
                            @if($customer->tax_id)<tr><th>Tax ID:</th><td>{{ $customer->tax_id }}</td></tr>@endif
                            <tr><th>Status:</th><td><span class="badge {{ $customer->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $customer->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
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
                        <h6 class="text-muted mb-2">Total Sales</h6>
                        <h3 class="mb-0">{{ $summary['total_sales'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="table-card">
                    <div class="p-3 text-center">
                        <h6 class="text-muted mb-2">Total Amount</h6>
                        <h3 class="mb-0 text-primary">৳{{ number_format($summary['total_amount'], 2) }}</h3>
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
                        <h6 class="text-muted mb-2">Unpaid Sales</h6>
                        <h4 class="mb-0"><span class="badge bg-danger">{{ $summary['unpaid_sales'] }}</span></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="table-card">
                    <div class="p-3 text-center">
                        <h6 class="text-muted mb-2">Partial Payments</h6>
                        <h4 class="mb-0"><span class="badge bg-warning text-dark">{{ $summary['partial_sales'] }}</span></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="table-card">
                    <div class="p-3 text-center">
                        <h6 class="text-muted mb-2">Paid Sales</h6>
                        <h4 class="mb-0"><span class="badge bg-success">{{ $summary['paid_sales'] }}</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transaction History -->
    <div class="col-md-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-list me-2"></i>Sales / Invoice Transactions</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
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
                        @forelse($allSales as $sale)
                            <tr>
                                <td><strong>{{ $sale->invoice_number }}</strong></td>
                                <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                <td><strong>৳{{ number_format($sale->total_amount, 2) }}</strong></td>
                                <td>৳{{ number_format($sale->paid_amount, 2) }}</td>
                                <td>
                                    @if($sale->due_amount > 0)
                                        <strong class="text-danger">৳{{ number_format($sale->due_amount, 2) }}</strong>
                                    @else
                                        <span class="text-success">৳0.00</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $sale->status === 'completed' ? 'bg-success' : ($sale->status === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $sale->payment_status === 'paid' ? 'bg-success' : ($sale->payment_status === 'partial' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ ucfirst($sale->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sales.print', $sale) }}" class="btn btn-sm btn-secondary" title="Print" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @if($sale->payment_status !== 'paid' && $sale->status === 'completed')
                                        <a href="{{ route('sales.edit', $sale) }}" class="btn btn-sm btn-warning" title="Record Payment">
                                            <i class="fas fa-money-bill"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No sales found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $allSales->links() }}
        </div>
    </div>
</div>
@endsection
