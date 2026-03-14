@extends('layouts.dashboard')

@section('title', 'Profit & Loss Report')
@section('page-title', 'Profit & Loss Report')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-file-invoice-dollar me-2"></i>Profit & Loss Report</h6>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('reports.financial.profit-loss', array_merge(request()->query(), ['export' => 'csv'])) }}"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.financial.profit-loss', array_merge(request()->query(), ['export' => 'xlsx'])) }}"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.financial.profit-loss', array_merge(request()->query(), ['export' => 'pdf'])) }}"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
            </ul>
        </div>
    </div>

    <div class="filter-wrapper">
    <form method="GET">
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
    </div>

    <!-- Summary Cards (module-stat-card style) -->
    <div class="p-4 border-bottom">
        <div class="row g-3 module-stats">
            <div class="col-md-4">
                <div class="module-stat-card rounded-3 p-3" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-left:4px solid #16a34a;">
                    <div class="small text-muted text-uppercase fw-bold">Total Revenue</div>
                    <div class="fw-bold fs-5" style="color:#166534;">৳{{ number_format($totalRevenue, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="module-stat-card rounded-3 p-3" style="background:linear-gradient(135deg,#fefce8,#fef9c3);border-left:4px solid #ca8a04;">
                    <div class="small text-muted text-uppercase fw-bold">Cost of Goods Sold</div>
                    <div class="fw-bold fs-5" style="color:#854d0e;">৳{{ number_format($cogs, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="module-stat-card rounded-3 p-3" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-left:4px solid #3b82f6;">
                    <div class="small text-muted text-uppercase fw-bold">Gross Profit Margin</div>
                    <div class="fw-bold fs-5" style="color:#1d4ed8;">{{ number_format($grossProfitMargin, 1) }}%</div>
                </div>
            </div>
        </div>
        <div class="row g-3 mt-2 module-stats">
            <div class="col-md-6">
                <div class="module-stat-card rounded-3 p-3" style="background:{{ $grossProfit >= 0 ? 'linear-gradient(135deg,#f0fdf4,#dcfce7)' : 'linear-gradient(135deg,#fef2f2,#fee2e2)' }};border-left:4px solid {{ $grossProfit >= 0 ? '#16a34a' : '#ef4444' }};">
                    <div class="small text-muted text-uppercase fw-bold">Gross Profit</div>
                    <div class="fw-bold fs-5" style="color:{{ $grossProfit >= 0 ? '#166534' : '#991b1b' }};">৳{{ number_format($grossProfit, 2) }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="module-stat-card rounded-3 p-3" style="background:{{ $netProfit >= 0 ? 'linear-gradient(135deg,#f0fdf4,#dcfce7)' : 'linear-gradient(135deg,#fef2f2,#fee2e2)' }};border-left:4px solid {{ $netProfit >= 0 ? '#16a34a' : '#ef4444' }};">
                    <div class="small text-muted text-uppercase fw-bold">Net Profit</div>
                    <div class="fw-bold fs-5" style="color:{{ $netProfit >= 0 ? '#166534' : '#991b1b' }};">৳{{ number_format($netProfit, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Breakdown -->
    <div class="p-4">
        <div class="row">
            <div class="col-md-6">
                <h6 class="mb-3">Revenue & COGS</h6>
                <table class="table table-bordered">
                    <tr>
                        <th>Total Revenue</th>
                        <td class="text-end">৳{{ number_format($totalRevenue, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Cost of Goods Sold</th>
                        <td class="text-end">৳{{ number_format($cogs, 2) }}</td>
                    </tr>
                    <tr class="table-success">
                        <th><strong>Gross Profit</strong></th>
                        <td class="text-end"><strong>৳{{ number_format($grossProfit, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Gross Profit Margin</th>
                        <td class="text-end">{{ number_format($grossProfitMargin, 2) }}%</td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h6 class="mb-3">Expenses & Net Profit</h6>
                <table class="table table-bordered">
                    <tr>
                        <th>Total Expenses</th>
                        <td class="text-end">৳{{ number_format($totalExpenses, 2) }}</td>
                    </tr>
                    <tr class="{{ $netProfit >= 0 ? 'table-success' : 'table-danger' }}">
                        <th><strong>Net Profit/Loss</strong></th>
                        <td class="text-end"><strong>৳{{ number_format($netProfit, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Net Profit Margin</th>
                        <td class="text-end">{{ number_format($netProfitMargin, 2) }}%</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

