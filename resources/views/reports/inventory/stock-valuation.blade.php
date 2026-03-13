@extends('layouts.dashboard')

@section('title', 'Stock Valuation Report')
@section('page-title', 'Stock Valuation Report')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-calculator me-2"></i>Stock Valuation Report</h6>
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
    
    <!-- Statistics -->
    <div class="p-4 border-bottom">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="stat-card info">
                    <div class="stat-icon"><i class="fas fa-box"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Products</div>
                        <div class="stat-value">{{ $stats['total_products'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Stock Value (Cost)</div>
                        <div class="stat-value">৳{{ number_format($stats['total_stock_value_cost'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="stat-icon"><i class="fas fa-money-bill"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Stock Value (Selling)</div>
                        <div class="stat-value">৳{{ number_format($stats['total_stock_value_selling'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card primary">
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Potential Profit</div>
                        <div class="stat-value">৳{{ number_format($stats['total_potential_profit'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Products Table -->
    <div class="table-responsive p-4">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th class="text-end">Stock Qty</th>
                    <th class="text-end">Cost Price</th>
                    <th class="text-end">Selling Price</th>
                    <th class="text-end">Cost Value</th>
                    <th class="text-end">Selling Value</th>
                    <th class="text-end">Potential Profit</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $item)
                    <tr>
                        <td><strong>{{ $item['product']->name }}</strong></td>
                        <td><code>{{ $item['product']->sku }}</code></td>
                        <td>{{ $item['product']->category->name ?? '-' }}</td>
                        <td class="text-end">{{ number_format($item['stock_quantity']) }}</td>
                        <td class="text-end">৳{{ number_format($item['cost_price'], 2) }}</td>
                        <td class="text-end">৳{{ number_format($item['selling_price'], 2) }}</td>
                        <td class="text-end">৳{{ number_format($item['total_cost_value'], 2) }}</td>
                        <td class="text-end">৳{{ number_format($item['total_selling_value'], 2) }}</td>
                        <td class="text-end text-success"><strong>৳{{ number_format($item['potential_profit'], 2) }}</strong></td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center">No products found.</td></tr>
                @endforelse
            </tbody>
            @if($products->count() > 0)
            <tfoot>
                <tr class="table-secondary">
                    <th colspan="3">Total</th>
                    <th class="text-end">{{ number_format($products->sum('stock_quantity')) }}</th>
                    <th colspan="2"></th>
                    <th class="text-end">৳{{ number_format($stats['total_stock_value_cost'], 2) }}</th>
                    <th class="text-end">৳{{ number_format($stats['total_stock_value_selling'], 2) }}</th>
                    <th class="text-end">৳{{ number_format($stats['total_potential_profit'], 2) }}</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

