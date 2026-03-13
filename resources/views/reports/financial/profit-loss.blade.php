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
    
    <!-- Summary Cards -->
    <div class="p-4 border-bottom">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="stat-card success">
                    <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-value">৳{{ number_format($totalRevenue, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card warning">
                    <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Cost of Goods Sold</div>
                        <div class="stat-value">৳{{ number_format($cogs, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card info">
                    <div class="stat-icon"><i class="fas fa-percentage"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Gross Profit Margin</div>
                        <div class="stat-value">{{ number_format($grossProfitMargin, 1) }}%</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <div class="stat-card {{ $grossProfit >= 0 ? 'success' : 'danger' }}">
                    <div class="stat-icon"><i class="fas fa-calculator"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Gross Profit</div>
                        <div class="stat-value">৳{{ number_format($grossProfit, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card {{ $netProfit >= 0 ? 'success' : 'danger' }}">
                    <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Net Profit</div>
                        <div class="stat-value">৳{{ number_format($netProfit, 2) }}</div>
                    </div>
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

