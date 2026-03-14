@extends('layouts.dashboard')

@section('title', 'Purchase Report')
@section('page-title', 'Purchase Report')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-shopping-bag me-2"></i>Purchase Report</h6>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('reports.purchases.index', array_merge(request()->query(), ['export' => 'csv'])) }}"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.purchases.index', array_merge(request()->query(), ['export' => 'xlsx'])) }}"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.purchases.index', array_merge(request()->query(), ['export' => 'pdf'])) }}"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
            </ul>
        </div>
    </div>

    <div class="filter-wrapper">
    <form method="GET">
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
                <button type="submit" class="btn btn-primary w-100">Generate</button>
            </div>
        </div>
    </form>
    </div>

    <!-- Statistics -->
    <div class="p-4 border-bottom">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Purchases</div>
                        <div class="stat-value">{{ $stats['total_purchases'] }}</div>
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
        
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="alert alert-info mb-0">
                    <strong>Average Purchase:</strong> ৳{{ number_format($stats['average_purchase'], 2) }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Purchases Table -->
    <div class="table-responsive p-4">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th class="text-end">Total Amount</th>
                    <th class="text-end">Paid</th>
                    <th class="text-end">Due</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                    <tr>
                        <td><a href="{{ route('purchases.show', $purchase) }}">{{ $purchase->po_number }}</a></td>
                        <td>{{ $purchase->order_date->format('d M Y') }}</td>
                        <td>{{ $purchase->supplier->name }}</td>
                        <td class="text-end"><strong>৳{{ number_format($purchase->total_amount, 2) }}</strong></td>
                        <td class="text-end">৳{{ number_format($purchase->paid_amount, 2) }}</td>
                        <td class="text-end text-danger">৳{{ number_format($purchase->due_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $purchase->status == 'received' ? 'success' : 'warning' }}">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No purchases found for this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
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
</script>
@endpush
@endsection

