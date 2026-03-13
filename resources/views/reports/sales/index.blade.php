@extends('layouts.dashboard')

@section('title', 'Sales Report')
@section('page-title', 'Sales Report')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-chart-line me-2"></i>Sales Report</h6>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('reports.sales.index', ['format' => 'csv'] + request()->query()) }}"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.sales.index', ['format' => 'excel'] + request()->query()) }}"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.sales.index', ['format' => 'pdf'] + request()->query()) }}"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="p-4 border-bottom">
        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label">Period</label>
                <select class="form-select" name="period" onchange="updateDates()">
                    <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ $period == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ $period == 'year' ? 'selected' : '' }}>This Year</option>
                    <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" class="form-control" name="date_from" id="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" class="form-control" name="date_to" id="date_to" value="{{ $dateTo }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Generate Report</button>
            </div>
        </div>
    </form>
    
    <!-- Statistics -->
    <div class="p-4 border-bottom">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Sales</div>
                        <div class="stat-value">{{ $stats['total_sales'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card info">
                    <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Amount</div>
                        <div class="stat-value">৳{{ number_format($stats['total_amount'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="stat-icon"><i class="fas fa-money-check-alt"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Paid</div>
                        <div class="stat-value">৳{{ number_format($stats['total_paid'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card danger">
                    <div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Due</div>
                        <div class="stat-value">৳{{ number_format($stats['total_due'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <div class="module-stat-card" style="border-left:3px solid #8b5cf6;">
                    <div class="msc-label">Average Sale Value</div>
                    <div class="msc-value" style="font-size:18px;color:#8b5cf6;">৳{{ number_format($stats['average_sale'], 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="module-stat-card" style="border-left:3px solid #f97316;">
                    <div class="msc-label">Total Discount Given</div>
                    <div class="msc-value" style="font-size:18px;color:#f97316;">৳{{ number_format($stats['total_discount'], 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="module-stat-card" style="border-left:3px solid #6b7280;">
                    <div class="msc-label">Total Tax Collected</div>
                    <div class="msc-value" style="font-size:18px;color:#6b7280;">৳{{ number_format($stats['total_tax'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Daily Trend Chart -->
    @if($dailyBreakdown->count() > 0)
    <div class="p-4 border-bottom">
        <h6 class="mb-3">Sales Trend</h6>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="salesTrendChart"></canvas>
        </div>
    </div>
    @endif
    
    <!-- Sales Table -->
    <div class="p-4">
        <h6 class="mb-3">Sales Details</h6>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Due</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td><a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_number }}</a></td>
                            <td>{{ $sale->sale_date->format('d M Y') }}</td>
                            <td>{{ $sale->customer_name ?? ($sale->customer->name ?? 'Walk-in') }}</td>
                            <td class="text-end"><strong>৳{{ number_format($sale->total_amount, 2) }}</strong></td>
                            <td class="text-end">৳{{ number_format($sale->paid_amount, 2) }}</td>
                            <td class="text-end text-danger">৳{{ number_format($sale->due_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $sale->payment_status == 'paid' ? 'success' : ($sale->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($sale->payment_status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No sales found for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function updateDates() {
    const period = document.querySelector('[name="period"]').value;
    const today = new Date();
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    
    if (period === 'today') {
        dateFrom.value = today.toISOString().split('T')[0];
        dateTo.value = today.toISOString().split('T')[0];
    } else if (period === 'week') {
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - today.getDay());
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);
        dateFrom.value = startOfWeek.toISOString().split('T')[0];
        dateTo.value = endOfWeek.toISOString().split('T')[0];
    } else if (period === 'month') {
        const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        dateFrom.value = startOfMonth.toISOString().split('T')[0];
        dateTo.value = endOfMonth.toISOString().split('T')[0];
    } else if (period === 'year') {
        const startOfYear = new Date(today.getFullYear(), 0, 1);
        const endOfYear = new Date(today.getFullYear(), 11, 31);
        dateFrom.value = startOfYear.toISOString().split('T')[0];
        dateTo.value = endOfYear.toISOString().split('T')[0];
    }
}

@if($dailyBreakdown->count() > 0)
const ctx = document.getElementById('salesTrendChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($dailyBreakdown->pluck('date')->toArray()) !!},
        datasets: [{
            label: 'Sales Amount',
            data: {!! json_encode($dailyBreakdown->pluck('total')->toArray()) !!},
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22, 163, 74, 0.1)',
            tension: 0.4,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#16a34a',
            pointBorderWidth: 2,
            pointRadius: 4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2.5,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        }
    }
});
@endif
</script>
@endpush
@endsection

