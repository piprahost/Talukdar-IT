@extends('layouts.dashboard')

@section('title', 'Payments')
@section('page-title', 'Payment Management')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-money-bill-wave me-2"></i>All Payments</h6>
        <div>
            <a href="{{ route('payments.create', ['type' => 'customer']) }}" class="btn btn-success btn-sm me-2">
                <i class="fas fa-plus me-2"></i>Customer Payment
            </a>
            <a href="{{ route('payments.create', ['type' => 'supplier']) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-2"></i>Supplier Payment
            </a>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="alert alert-success mb-0">
                <strong>Total Customer Payments:</strong> ৳{{ number_format($totalCustomerPayments, 2) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-info mb-0">
                <strong>Total Supplier Payments:</strong> ৳{{ number_format($totalSupplierPayments, 2) }}
            </div>
        </div>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3 mb-2">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search payment, reference, customer...">
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-select" name="type" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="customer" {{ request('type')=='customer'?'selected':'' }}>Customer</option>
                    <option value="supplier" {{ request('type')=='supplier'?'selected':'' }}>Supplier</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-select" name="payment_method" onchange="this.form.submit()">
                    <option value="">All Methods</option>
                    <option value="cash" {{ request('payment_method')=='cash'?'selected':'' }}>Cash</option>
                    <option value="card" {{ request('payment_method')=='card'?'selected':'' }}>Card</option>
                    <option value="mobile_banking" {{ request('payment_method')=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                    <option value="bank_transfer" {{ request('payment_method')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="From Date">
            </div>
            <div class="col-md-2 mb-2">
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="To Date">
            </div>
            <div class="col-md-1 mb-2">
                <button type="submit" class="btn btn-outline-primary w-100">Search</button>
            </div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Payment #</th>
                    <th>Type</th>
                    <th>Reference</th>
                    <th>Customer/Supplier</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td><strong>{{ $payment->payment_number }}</strong></td>
                        <td>
                            <span class="badge {{ $payment->payment_type === 'customer' ? 'bg-success' : 'bg-primary' }}">
                                {{ ucfirst($payment->payment_type) }}
                            </span>
                        </td>
                        <td>
                            @if($payment->payment_type === 'customer' && $payment->sale)
                                <a href="{{ route('sales.show', $payment->sale) }}" class="text-primary">
                                    {{ $payment->sale->invoice_number }}
                                </a>
                            @elseif($payment->payment_type === 'supplier' && $payment->purchase)
                                <a href="{{ route('purchases.show', $payment->purchase) }}" class="text-primary">
                                    {{ $payment->purchase->po_number }}
                                </a>
                            @endif
                        </td>
                        <td>
                            @if($payment->customer)
                                {{ $payment->customer->name }}
                            @elseif($payment->supplier)
                                {{ $payment->supplier->name }}
                            @endif
                        </td>
                        <td><strong>৳{{ number_format($payment->amount, 2) }}</strong></td>
                        <td>{{ $payment->payment_method_name }}</td>
                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('payments.show', $payment) }}" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('payments.edit', $payment) }}" class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $payments->links() }}
</div>
@endsection

