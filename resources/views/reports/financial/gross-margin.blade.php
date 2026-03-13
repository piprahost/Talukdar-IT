@extends('layouts.dashboard')

@section('title', 'Gross Margin by Product')
@section('page-title', 'Gross Margin by Product')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-percentage me-2"></i>Gross Margin Analysis by Product</h6>
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
                    <th>Product</th>
                    <th class="text-end">Qty Sold</th>
                    <th class="text-end">Revenue</th>
                    <th class="text-end">Cost</th>
                    <th class="text-end">Gross Profit</th>
                    <th class="text-end">Margin %</th>
                    <th class="text-end">Avg Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($marginAnalysis as $item)
                    <tr>
                        <td>
                            <strong>{{ $item['product']->name }}</strong>
                            <br><small class="text-muted">{{ $item['product']->category->name ?? '' }}</small>
                        </td>
                        <td class="text-end">{{ number_format($item['quantity']) }}</td>
                        <td class="text-end">৳{{ number_format($item['revenue'], 2) }}</td>
                        <td class="text-end">৳{{ number_format($item['cost'], 2) }}</td>
                        <td class="text-end">
                            <strong class="{{ $item['gross_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                ৳{{ number_format($item['gross_profit'], 2) }}
                            </strong>
                        </td>
                        <td class="text-end">
                            <span class="badge bg-{{ $item['margin'] >= 30 ? 'success' : ($item['margin'] >= 15 ? 'info' : ($item['margin'] >= 0 ? 'warning' : 'danger')) }}">
                                {{ number_format($item['margin'], 1) }}%
                            </span>
                        </td>
                        <td class="text-end">৳{{ number_format($item['avg_selling_price'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No margin data found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

