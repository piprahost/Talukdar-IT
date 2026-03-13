@extends('layouts.dashboard')

@section('title', 'Top Selling Products')
@section('page-title', 'Top Selling Products')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-star me-2"></i>Top Selling Products</h6>
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
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Top N Products</label>
                <select class="form-select" name="limit" onchange="this.form.submit()">
                    <option value="5" {{ $limit == 5 ? 'selected' : '' }}>Top 5</option>
                    <option value="10" {{ $limit == 10 ? 'selected' : '' }}>Top 10</option>
                    <option value="20" {{ $limit == 20 ? 'selected' : '' }}>Top 20</option>
                    <option value="50" {{ $limit == 50 ? 'selected' : '' }}>Top 50</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Generate</button>
            </div>
        </div>
    </form>
    
    <div class="table-responsive p-4">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 50px;">Rank</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th class="text-end">Quantity Sold</th>
                    <th class="text-end">Total Amount</th>
                    <th class="text-end">Avg Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProducts as $index => $item)
                    <tr>
                        <td>
                            @if($index == 0)
                                <span class="badge bg-warning text-dark">🥇 1</span>
                            @elseif($index == 1)
                                <span class="badge bg-secondary">🥈 2</span>
                            @elseif($index == 2)
                                <span class="badge bg-danger">🥉 3</span>
                            @else
                                <strong>#{{ $index + 1 }}</strong>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                            @if($item->product)
                                <br><small class="text-muted">{{ $item->product->category->name ?? '' }}</small>
                            @endif
                        </td>
                        <td><code>{{ $item->product->sku ?? 'N/A' }}</code></td>
                        <td class="text-end"><strong>{{ number_format($item->total_quantity) }}</strong></td>
                        <td class="text-end"><strong>৳{{ number_format($item->total_amount, 2) }}</strong></td>
                        <td class="text-end">৳{{ number_format($item->avg_price, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No sales data found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

