@extends('layouts.dashboard')

@section('title', 'Cost Analysis Report')
@section('page-title', 'Cost Analysis Report')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-dollar-sign me-2"></i>Cost Analysis Report</h6>
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
                    <th>SKU</th>
                    <th class="text-end">Quantity Purchased</th>
                    <th class="text-end">Avg Cost Price</th>
                    <th class="text-end">Total Cost</th>
                </tr>
            </thead>
            <tbody>
                @forelse($costAnalysis as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                            @if($item->product)
                                <br><small class="text-muted">{{ $item->product->category->name ?? '' }}</small>
                            @endif
                        </td>
                        <td><code>{{ $item->product->sku ?? 'N/A' }}</code></td>
                        <td class="text-end"><strong>{{ number_format($item->total_quantity) }}</strong></td>
                        <td class="text-end">৳{{ number_format($item->avg_cost_price, 2) }}</td>
                        <td class="text-end"><strong>৳{{ number_format($item->total_cost, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No purchase data found.</td></tr>
                @endforelse
            </tbody>
            @if($costAnalysis->count() > 0)
            <tfoot>
                <tr class="table-secondary">
                    <th colspan="2">Total</th>
                    <th class="text-end">{{ number_format($costAnalysis->sum('total_quantity')) }}</th>
                    <th></th>
                    <th class="text-end">৳{{ number_format($costAnalysis->sum('total_cost'), 2) }}</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

