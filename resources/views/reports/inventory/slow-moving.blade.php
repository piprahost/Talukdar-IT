@extends('layouts.dashboard')

@section('title', 'Slow Moving Products')
@section('page-title', 'Slow Moving Products')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-hourglass-half me-2"></i>Slow Moving Products</h6>
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
                <label class="form-label">Days Threshold</label>
                <select class="form-select" name="days" onchange="this.form.submit()">
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Days</option>
                    <option value="60" {{ $days == 60 ? 'selected' : '' }}>60 Days</option>
                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 Days</option>
                    <option value="180" {{ $days == 180 ? 'selected' : '' }}>180 Days</option>
                </select>
            </div>
            <div class="col-md-8 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Generate</button>
                <small class="text-muted ms-3">Products not sold in the last {{ $days }} days</small>
            </div>
        </div>
    </form>
    
    <div class="table-responsive p-4">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th class="text-end">Stock Qty</th>
                    <th class="text-end">Stock Value</th>
                    <th>Last Sale Date</th>
                    <th>Days Since Sale</th>
                </tr>
            </thead>
            <tbody>
                @forelse($slowMoving as $item)
                    <tr>
                        <td><strong>{{ $item['product']->name }}</strong></td>
                        <td><code>{{ $item['product']->sku }}</code></td>
                        <td>{{ $item['product']->category->name ?? '-' }}</td>
                        <td class="text-end">{{ number_format($item['product']->stock_quantity) }}</td>
                        <td class="text-end"><strong>৳{{ number_format($item['stock_value'], 2) }}</strong></td>
                        <td>{{ $item['last_sale_date'] }}</td>
                        <td>
                            @if($item['days_since_sale'])
                                <span class="badge bg-{{ $item['days_since_sale'] > 90 ? 'danger' : ($item['days_since_sale'] > 60 ? 'warning' : 'info') }}">
                                    {{ $item['days_since_sale'] }} days
                                </span>
                            @else
                                <span class="badge bg-danger">Never</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No slow moving products found. All products are selling well!</td></tr>
                @endforelse
            </tbody>
            @if($slowMoving->count() > 0)
            <tfoot>
                <tr class="table-secondary">
                    <th colspan="4">Total Stock Value</th>
                    <th class="text-end">৳{{ number_format($slowMoving->sum('stock_value'), 2) }}</th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

