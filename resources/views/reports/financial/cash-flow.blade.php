@extends('layouts.dashboard')

@section('title', 'Cash Flow Statement')
@section('page-title', 'Cash Flow Statement')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-exchange-alt me-2"></i>Cash Flow Statement</h6>
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
    
    <!-- Summary -->
    <div class="p-4 border-bottom">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="stat-card success">
                    <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Cash In</div>
                        <div class="stat-value">৳{{ number_format($totalCashIn, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card danger">
                    <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Cash Out</div>
                        <div class="stat-value">৳{{ number_format($totalCashOut, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card {{ $netCashFlow >= 0 ? 'info' : 'warning' }}">
                    <div class="stat-icon"><i class="fas fa-balance-scale"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Net Cash Flow</div>
                        <div class="stat-value">৳{{ number_format($netCashFlow, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <h6>Cash Inflows</h6>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Customer Payments</span>
                        <strong>৳{{ number_format($customerPayments, 2) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Direct Sales (Paid at Sale)</span>
                        <strong>৳{{ number_format($directSales, 2) }}</strong>
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>Cash Outflows</h6>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Supplier Payments</span>
                        <strong>৳{{ number_format($supplierPayments, 2) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Expenses</span>
                        <strong>৳{{ number_format($expenses, 2) }}</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Daily Flow Chart -->
    @if($dailyFlow->count() > 0)
    <div class="p-4 border-bottom">
        <h6 class="mb-3">Daily Cash Flow Trend</h6>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="cashFlowChart"></canvas>
        </div>
    </div>
    @endif
    
    <!-- Daily Breakdown Table -->
    <div class="p-4">
        <h6 class="mb-3">Daily Breakdown</h6>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-end">Cash In</th>
                        <th class="text-end">Cash Out</th>
                        <th class="text-end">Net Flow</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyFlow as $day)
                        <tr>
                            <td>{{ $day['date_label'] }}</td>
                            <td class="text-end text-success">৳{{ number_format($day['cash_in'], 2) }}</td>
                            <td class="text-end text-danger">৳{{ number_format($day['cash_out'], 2) }}</td>
                            <td class="text-end">
                                <strong class="{{ $day['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    ৳{{ number_format($day['net'], 2) }}
                                </strong>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if($dailyFlow->count() > 0)
const ctx = document.getElementById('cashFlowChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($dailyFlow->pluck('date_label')->toArray()) !!},
        datasets: [{
            label: 'Cash In',
            data: {!! json_encode($dailyFlow->pluck('cash_in')->toArray()) !!},
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.2)',
            tension: 0.1
        }, {
            label: 'Cash Out',
            data: {!! json_encode($dailyFlow->pluck('cash_out')->toArray()) !!},
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.2)',
            tension: 0.1
        }, {
            label: 'Net Flow',
            data: {!! json_encode($dailyFlow->pluck('net')->toArray()) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2.5,
        scales: {
            y: {
                beginAtZero: false
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

