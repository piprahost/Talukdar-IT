@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
    <!-- Stats Cards Row 1 -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-value">৳{{ number_format($monthSales, 2) }}</div>
                <div class="stat-label">Monthly Revenue</div>
                <div class="mt-2">
                    <small class="{{ $salesGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas fa-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }}"></i> 
                        {{ number_format(abs($salesGrowth), 1) }}% from last month
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-value">৳{{ number_format($monthPurchases, 2) }}</div>
                <div class="stat-label">Monthly Purchases</div>
                <div class="mt-2">
                    <small class="{{ $purchaseGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas fa-arrow-{{ $purchaseGrowth >= 0 ? 'up' : 'down' }}"></i> 
                        {{ number_format(abs($purchaseGrowth), 1) }}% from last month
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value">৳{{ number_format($monthProfit, 2) }}</div>
                <div class="stat-label">Monthly Profit</div>
                <div class="mt-2">
                    <small class="{{ $profitMargin >= 0 ? 'text-success' : 'text-danger' }}">
                        Margin: {{ number_format($profitMargin, 1) }}%
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-value">{{ number_format($totalProducts) }}</div>
                <div class="stat-label">Total Products</div>
                <div class="mt-2">
                    <small class="text-muted">
                        {{ $activeProducts }} active | {{ $lowStockProducts }} low stock
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards Row 2 -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-value">{{ number_format($lowStockProducts) }}</div>
                <div class="stat-label">Low Stock Alert</div>
                <div class="mt-2">
                    @if($lowStockProducts > 0)
                        <a href="{{ route('stock.low-stock') }}" class="btn btn-sm btn-danger">
                            View Details
                        </a>
                    @else
                        <small class="text-success">All products in stock</small>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">{{ number_format($totalCustomers) }}</div>
                <div class="stat-label">Total Customers</div>
                <div class="mt-2">
                    <small class="text-muted">৳{{ number_format($customerDues, 2) }} due</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="stat-card secondary">
                <div class="stat-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="stat-value">{{ number_format($totalSuppliers) }}</div>
                <div class="stat-label">Total Suppliers</div>
                <div class="mt-2">
                    <small class="text-muted">৳{{ number_format($supplierDues, 2) }} due</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-value">৳{{ number_format($todaySales, 2) }}</div>
                <div class="stat-label">Today's Sales</div>
                <div class="mt-2">
                    <small class="text-muted">{{ \Carbon\Carbon::today()->format('M d, Y') }}</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="table-card">
                <div class="table-card-header">
                    <h6><i class="fas fa-chart-line me-2"></i>Sales Trend (Last 30 Days)</h6>
                </div>
                <canvas id="salesChart" height="80"></canvas>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="table-card">
                <div class="table-card-header">
                    <h6><i class="fas fa-chart-pie me-2"></i>Payment Status</h6>
                </div>
                <canvas id="paymentChart" height="200"></canvas>
                <div class="mt-3">
                    @php
                        $paid = $salesPaymentStatus->get('paid')->total ?? 0;
                        $partial = $salesPaymentStatus->get('partial')->total ?? 0;
                        $unpaid = $salesPaymentStatus->get('unpaid')->total ?? 0;
                    @endphp
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle text-success"></i> Paid:</span>
                        <strong>৳{{ number_format($paid, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle text-warning"></i> Partial:</span>
                        <strong>৳{{ number_format($partial, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-circle text-danger"></i> Unpaid:</span>
                        <strong>৳{{ number_format($unpaid, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Products & Recent Sales -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <h6><i class="fas fa-star me-2"></i>Top Selling Products (Last 30 Days)</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $product)
                                <tr>
                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                        <br><small class="text-muted">{{ $product->sku }}</small>
                                    </td>
                                    <td><span class="badge bg-primary">{{ $product->total_qty }}</span></td>
                                    <td><strong>৳{{ number_format($product->total_revenue, 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">No sales data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="table-card">
                <div class="table-card-header">
                    <h6><i class="fas fa-clock me-2"></i>Recent Sales</h6>
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.show', $sale) }}" class="text-primary">
                                            {{ $sale->invoice_number }}
                                        </a>
                                    </td>
                                    <td>{{ $sale->customer_name ?? ($sale->customer->name ?? 'Walk-in') }}</td>
                                    <td><strong>৳{{ number_format($sale->total_amount, 2) }}</strong></td>
                                    <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge 
                                            {{ $sale->payment_status === 'paid' ? 'bg-success' : 
                                               ($sale->payment_status === 'partial' ? 'bg-warning' : 'bg-danger') }}">
                                            {{ ucfirst($sale->payment_status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">No recent sales</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Links & Alerts -->
    <div class="row g-4">
        <div class="col-md-12">
            <div class="table-card">
                <div class="table-card-header">
                    <h6><i class="fas fa-bell me-2"></i>Quick Alerts & Actions</h6>
                </div>
                <div class="row g-3 p-3">
                    @if($lowStockProducts > 0)
                    <div class="col-md-3">
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>{{ $lowStockProducts }}</strong> products are low in stock
                            <br><a href="{{ route('stock.low-stock') }}" class="btn btn-sm btn-warning mt-2">View</a>
                        </div>
                    </div>
                    @endif
                    
                    @if($customerDues > 0)
                    <div class="col-md-3">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            <strong>৳{{ number_format($customerDues, 2) }}</strong> in customer dues
                            <br><a href="{{ route('customers.index') }}" class="btn btn-sm btn-info mt-2">View</a>
                        </div>
                    </div>
                    @endif
                    
                    @if($supplierDues > 0)
                    <div class="col-md-3">
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-credit-card me-2"></i>
                            <strong>৳{{ number_format($supplierDues, 2) }}</strong> in supplier dues
                            <br><a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary mt-2">View</a>
                        </div>
                    </div>
                    @endif
                    
                    @if($pendingServices > 0)
                    <div class="col-md-3">
                        <div class="alert alert-primary mb-0">
                            <i class="fas fa-laptop-medical me-2"></i>
                            <strong>{{ $pendingServices }}</strong> pending services
                            <br><a href="{{ route('services.index') }}" class="btn btn-sm btn-primary mt-2">View</a>
                        </div>
                    </div>
                    @endif
                    
                    @if($pendingWarranties > 0)
                    <div class="col-md-3">
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>{{ $pendingWarranties }}</strong> warranty submissions
                            <br><a href="{{ route('warranty-submissions.index') }}" class="btn btn-sm btn-success mt-2">View</a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: @json($salesChartData->pluck('date')),
        datasets: [{
            label: 'Sales (BDT)',
            data: @json($salesChartData->pluck('total')),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '৳' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Payment Status Chart
const paymentCtx = document.getElementById('paymentChart').getContext('2d');
const paymentChart = new Chart(paymentCtx, {
    type: 'doughnut',
    data: {
        labels: ['Paid', 'Partial', 'Unpaid'],
        datasets: [{
            data: [
                {{ $salesPaymentStatus->get('paid')->total ?? 0 }},
                {{ $salesPaymentStatus->get('partial')->total ?? 0 }},
                {{ $salesPaymentStatus->get('unpaid')->total ?? 0 }}
            ],
            backgroundColor: [
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(255, 99, 132, 0.8)'
            ],
            borderColor: [
                'rgb(75, 192, 192)',
                'rgb(255, 206, 86)',
                'rgb(255, 99, 132)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endpush
