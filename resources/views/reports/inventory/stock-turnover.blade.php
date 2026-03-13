@extends('layouts.dashboard')

@section('title', 'Stock Turnover Ratio')
@section('page-title', 'Stock Turnover Ratio')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-sync-alt me-2"></i>Stock Turnover Ratio Report</h6>
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
                <label class="form-label">Period (Days)</label>
                <select class="form-select" name="period" onchange="this.form.submit()">
                    <option value="30" {{ $period == 30 ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="60" {{ $period == 60 ? 'selected' : '' }}>Last 60 Days</option>
                    <option value="90" {{ $period == 90 ? 'selected' : '' }}>Last 90 Days</option>
                    <option value="180" {{ $period == 180 ? 'selected' : '' }}>Last 180 Days</option>
                </select>
            </div>
            <div class="col-md-8 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Generate</button>
                <small class="text-muted ms-3">Turnover ratio = Quantity Sold / Average Stock</small>
            </div>
        </div>
    </form>
    
    <div class="table-responsive p-4">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th class="text-end">Avg Stock</th>
                    <th class="text-end">Sold in Period</th>
                    <th class="text-end">Turnover Ratio</th>
                    <th class="text-end">Days to Turnover</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $item)
                    <tr>
                        <td>
                            <strong>{{ $item['product']->name }}</strong>
                            <br><small class="text-muted">{{ $item['product']->category->name ?? '' }}</small>
                        </td>
                        <td><code>{{ $item['product']->sku }}</code></td>
                        <td class="text-end">{{ number_format($item['average_stock']) }}</td>
                        <td class="text-end"><strong>{{ number_format($item['sold_in_period']) }}</strong></td>
                        <td class="text-end">
                            <span class="badge bg-{{ $item['turnover_ratio'] >= 2 ? 'success' : ($item['turnover_ratio'] >= 1 ? 'info' : ($item['turnover_ratio'] > 0 ? 'warning' : 'secondary')) }}">
                                {{ number_format($item['turnover_ratio'], 2) }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($item['days_to_turnover'])
                                {{ number_format($item['days_to_turnover'], 1) }} days
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No data found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

