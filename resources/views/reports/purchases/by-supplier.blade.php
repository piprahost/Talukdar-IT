@extends('layouts.dashboard')

@section('title', 'Purchases by Supplier')
@section('page-title', 'Purchases by Supplier')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-truck me-2"></i>Purchases by Supplier</h6>
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
                    <th>Supplier</th>
                    <th class="text-end">Total Orders</th>
                    <th class="text-end">Total Amount</th>
                    <th class="text-end">Total Paid</th>
                    <th class="text-end">Total Due</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchasesBySupplier as $item)
                    <tr>
                        <td>
                            @if($item->supplier)
                                <a href="{{ route('suppliers.show', $item->supplier) }}"><strong>{{ $item->supplier->name }}</strong></a>
                                @if($item->supplier->company_name)
                                    <br><small class="text-muted">{{ $item->supplier->company_name }}</small>
                                @endif
                            @endif
                        </td>
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
                    <tr><td colspan="5" class="text-center">No purchase data found.</td></tr>
                @endforelse
            </tbody>
            @if($purchasesBySupplier->count() > 0)
            <tfoot>
                <tr class="table-secondary">
                    <th>Total</th>
                    <th class="text-end">{{ number_format($purchasesBySupplier->sum('total_orders')) }}</th>
                    <th class="text-end">৳{{ number_format($purchasesBySupplier->sum('total_amount'), 2) }}</th>
                    <th class="text-end">৳{{ number_format($purchasesBySupplier->sum('total_paid'), 2) }}</th>
                    <th class="text-end text-danger">৳{{ number_format($purchasesBySupplier->sum('total_due'), 2) }}</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

