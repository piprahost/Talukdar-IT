@extends('layouts.dashboard')

@section('title', 'Sales by Customer')
@section('page-title', 'Sales by Customer')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-users me-2"></i>Sales by Customer</h6>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
            </ul>
        </div>
    </div>
    
    <form method="GET" class="p-4 border-bottom">
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label">From Date</label>
                <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">To Date</label>
                <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Generate</button>
            </div>
        </div>
    </form>
    
    <div class="table-responsive p-4">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th class="text-end">Total Orders</th>
                    <th class="text-end">Total Amount</th>
                    <th class="text-end">Total Paid</th>
                    <th class="text-end">Total Due</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salesByCustomer as $item)
                    <tr>
                        <td>
                            @if($item->customer)
                                <a href="{{ route('customers.show', $item->customer) }}"><strong>{{ $item->customer->name }}</strong></a>
                            @else
                                <strong>Walk-in Customer</strong>
                            @endif
                        </td>
                        <td>{{ $item->customer->phone ?? '-' }}</td>
                        <td class="text-end"><span class="badge bg-info">{{ $item->total_orders }}</span></td>
                        <td class="text-end"><strong>৳{{ number_format($item->total_amount, 2) }}</strong></td>
                        <td class="text-end">৳{{ number_format($item->total_paid, 2) }}</td>
                        <td class="text-end">
                            @if($item->total_due > 0)
                                <span class="text-danger"><strong>৳{{ number_format($item->total_due, 2) }}</strong></span>
                            @else
                                <span class="text-success">৳0.00</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No sales data found.</td></tr>
                @endforelse
            </tbody>
            @if($salesByCustomer->count() > 0)
            <tfoot>
                <tr class="table-secondary">
                    <th colspan="2">Total</th>
                    <th class="text-end">{{ number_format($salesByCustomer->sum('total_orders')) }}</th>
                    <th class="text-end">৳{{ number_format($salesByCustomer->sum('total_amount'), 2) }}</th>
                    <th class="text-end">৳{{ number_format($salesByCustomer->sum('total_paid'), 2) }}</th>
                    <th class="text-end text-danger">৳{{ number_format($salesByCustomer->sum('total_due'), 2) }}</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

