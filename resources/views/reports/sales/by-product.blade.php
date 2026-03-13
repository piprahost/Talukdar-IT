@extends('layouts.dashboard')

@section('title', 'Sales by Product')
@section('page-title', 'Sales by Product')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-box me-2"></i>Sales by Product</h6>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('reports.sales.by-product', ['format'=>'csv']+request()->query()) }}"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.sales.by-product', ['format'=>'excel']+request()->query()) }}"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.sales.by-product', ['format'=>'pdf']+request()->query()) }}"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
            </ul>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-wrapper">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;color:#6b7280;">From Date</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;color:#6b7280;">To Date</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ $dateTo }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-chart-bar me-1"></i>Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Summary Stats --}}
    @if($salesByProduct->count() > 0)
    <div class="p-4 border-bottom">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="module-stat-card">
                    <div class="msc-label">Products Sold</div>
                    <div class="msc-value">{{ $salesByProduct->count() }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="module-stat-card" style="border-left:3px solid #3b82f6;">
                    <div class="msc-label">Total Units</div>
                    <div class="msc-value" style="color:#3b82f6;">{{ number_format($salesByProduct->sum('total_quantity')) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="module-stat-card" style="border-left:3px solid #16a34a;">
                    <div class="msc-label">Total Revenue</div>
                    <div class="msc-value" style="font-size:16px;color:#16a34a;">৳{{ number_format($salesByProduct->sum('total_amount'), 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="module-stat-card" style="border-left:3px solid #8b5cf6;">
                    <div class="msc-label">Avg per Unit</div>
                    @php $totalQty = $salesByProduct->sum('total_quantity'); $totalAmt = $salesByProduct->sum('total_amount'); @endphp
                    <div class="msc-value" style="font-size:16px;color:#8b5cf6;">৳{{ $totalQty > 0 ? number_format($totalAmt / $totalQty, 2) : '0.00' }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category / Brand</th>
                    <th class="text-center">Qty Sold</th>
                    <th class="text-end">Total Revenue</th>
                    <th class="text-end">Avg Price</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salesByProduct as $i => $item)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td>
                        <strong style="font-size:14px;">{{ $item->product->name ?? 'N/A' }}</strong>
                    </td>
                    <td>
                        @if($item->product)
                        <code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:11px;">{{ $item->product->sku }}</code>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#6b7280;">
                        {{ $item->product->category->name ?? '' }}
                        @if($item->product && $item->product->brand)
                            · {{ $item->product->brand->name }}
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:#eff6ff;color:#1d4ed8;font-weight:700;font-size:13px;">{{ number_format($item->total_quantity) }}</span>
                    </td>
                    <td class="text-end fw-bold">৳{{ number_format($item->total_amount, 2) }}</td>
                    <td class="text-end" style="font-size:13px;color:#6b7280;">৳{{ number_format($item->total_amount / max($item->total_quantity, 1), 2) }}</td>
                    <td class="text-center">
                        @if($item->product)
                        <a href="{{ route('products.show', $item->product) }}" class="btn btn-sm btn-outline-primary" title="View Product">
                            <i class="fas fa-eye"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">No sales data found for this period.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($salesByProduct->count() > 0)
            <tfoot style="background:#f9fafb;">
                <tr>
                    <td colspan="4" class="text-end fw-bold">Totals</td>
                    <td class="text-center fw-bold">{{ number_format($salesByProduct->sum('total_quantity')) }}</td>
                    <td class="text-end fw-bold" style="color:#16a34a;">৳{{ number_format($salesByProduct->sum('total_amount'), 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
