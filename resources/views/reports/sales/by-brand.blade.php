@extends('layouts.dashboard')

@section('title', 'Sales by Brand')
@section('page-title', 'Sales by Brand')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-certificate me-2"></i>Sales by Brand</h6>
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
                    <th>Brand</th>
                    <th class="text-end">Quantity Sold</th>
                    <th class="text-end">Total Amount</th>
                    <th class="text-end">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = $salesByBrand->sum('total_amount');
                @endphp
                @forelse($salesByBrand as $item)
                    <tr>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td class="text-end"><strong>{{ number_format($item->total_quantity) }}</strong></td>
                        <td class="text-end"><strong>৳{{ number_format($item->total_amount, 2) }}</strong></td>
                        <td class="text-end">
                            @if($grandTotal > 0)
                                <span class="badge bg-info">{{ number_format(($item->total_amount / $grandTotal) * 100, 1) }}%</span>
                            @else
                                <span class="badge bg-secondary">0%</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No sales data found.</td></tr>
                @endforelse
            </tbody>
            @if($salesByBrand->count() > 0)
            <tfoot>
                <tr class="table-secondary">
                    <th>Total</th>
                    <th class="text-end">{{ number_format($salesByBrand->sum('total_quantity')) }}</th>
                    <th class="text-end">৳{{ number_format($grandTotal, 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

