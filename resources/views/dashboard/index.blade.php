@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Stats Overview -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-sm-6">
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
        
        <div class="col-lg-3 col-sm-6">
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
        
        <div class="col-lg-3 col-sm-6">
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
        
        <div class="col-lg-3 col-sm-6">
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
    
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-sm-6">
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
        
        <div class="col-lg-3 col-sm-6">
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
        
        <div class="col-lg-3 col-sm-6">
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
        
        <div class="col-lg-3 col-sm-6">
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
        <div class="col-xl-8">
            <div class="table-card">
                <div class="table-card-header">
                    <h6><i class="fas fa-chart-line me-2"></i>Sales Trend (Last 30 Days)</h6>
                </div>
                <div class="p-4">
                    <canvas id="salesChart" height="80"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="table-card">
                <div class="table-card-header">
                    <h6><i class="fas fa-chart-pie me-2"></i>Payment Status</h6>
                </div>
                <div class="p-4">
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
    </div>
    
    <!-- Top Products & Recent Sales -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
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
                            @forelse($topProducts->take(5) as $product)
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
        
        <div class="col-xl-6">
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
                            @forelse($recentSales->take(7) as $sale)
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
    
    <!-- Alerts & Quick Actions -->
    <div class="row g-4">
        {{-- Alerts --}}
        @if($lowStockProducts > 0 || $customerDues > 0 || $supplierDues > 0 || $pendingServices > 0 || $pendingWarranties > 0)
        <div class="col-xl-8">
            <div class="table-card">
                <div class="table-card-header">
                    <h6><i class="fas fa-bell me-2"></i>Alerts Requiring Attention</h6>
                </div>
                <div class="p-3">
                    <div class="row g-3">
                        @if($lowStockProducts > 0)
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:#fff7ed;border-left:4px solid #f97316;">
                                <div style="width:40px;height:40px;background:#fed7aa;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-exclamation-triangle" style="color:#c2410c;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div style="font-size:11px;color:#9a3412;font-weight:700;text-transform:uppercase;">Low Stock</div>
                                    <div style="font-size:18px;font-weight:800;color:#111;">{{ $lowStockProducts }} Products</div>
                                </div>
                                <a href="{{ route('stock.low-stock') }}" class="btn btn-sm btn-outline-warning">View</a>
                            </div>
                        </div>
                        @endif
                        @if($customerDues > 0)
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:#fef2f2;border-left:4px solid #ef4444;">
                                <div style="width:40px;height:40px;background:#fecaca;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-users" style="color:#b91c1c;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div style="font-size:11px;color:#991b1b;font-weight:700;text-transform:uppercase;">Customer Dues</div>
                                    <div style="font-size:18px;font-weight:800;color:#111;">৳{{ number_format($customerDues, 2) }}</div>
                                </div>
                                <a href="{{ route('sales.index', ['payment_status'=>'unpaid']) }}" class="btn btn-sm btn-outline-danger">View</a>
                            </div>
                        </div>
                        @endif
                        @if($supplierDues > 0)
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:#eff6ff;border-left:4px solid #3b82f6;">
                                <div style="width:40px;height:40px;background:#bfdbfe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-truck" style="color:#1d4ed8;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div style="font-size:11px;color:#1e40af;font-weight:700;text-transform:uppercase;">Supplier Dues</div>
                                    <div style="font-size:18px;font-weight:800;color:#111;">৳{{ number_format($supplierDues, 2) }}</div>
                                </div>
                                <a href="{{ route('purchases.index', ['payment_status'=>'unpaid']) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </div>
                        </div>
                        @endif
                        @if($pendingServices > 0)
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:#f0fdf4;border-left:4px solid #16a34a;">
                                <div style="width:40px;height:40px;background:#bbf7d0;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-laptop-medical" style="color:#15803d;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div style="font-size:11px;color:#166534;font-weight:700;text-transform:uppercase;">Pending Services</div>
                                    <div style="font-size:18px;font-weight:800;color:#111;">{{ $pendingServices }} Orders</div>
                                </div>
                                <a href="{{ route('services.index', ['status'=>'pending']) }}" class="btn btn-sm btn-outline-success">View</a>
                            </div>
                        </div>
                        @endif
                        @if($pendingWarranties > 0)
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:#f5f3ff;border-left:4px solid #8b5cf6;">
                                <div style="width:40px;height:40px;background:#ddd6fe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-shield-alt" style="color:#6d28d9;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div style="font-size:11px;color:#5b21b6;font-weight:700;text-transform:uppercase;">Warranty Submissions</div>
                                    <div style="font-size:18px;font-weight:800;color:#111;">{{ $pendingWarranties }} Pending</div>
                                </div>
                                <a href="{{ route('warranty-submissions.index') }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="col-xl-4">
            <div class="table-card h-100">
                <div class="table-card-header">
                    <h6><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                </div>
                <div class="p-3 d-flex flex-column gap-2">
                    @can('create sales')
                    <a href="{{ route('sales.create') }}" class="btn btn-primary w-100 text-start">
                        <i class="fas fa-file-invoice-dollar me-2"></i>New Sale / Invoice
                    </a>
                    @endcan
                    @can('create services')
                    <a href="{{ route('services.create') }}" class="btn btn-outline-primary w-100 text-start">
                        <i class="fas fa-laptop-medical me-2"></i>New Service Order
                    </a>
                    @endcan
                    @can('create purchases')
                    <a href="{{ route('purchases.create') }}" class="btn btn-outline-primary w-100 text-start">
                        <i class="fas fa-shopping-cart me-2"></i>New Purchase Order
                    </a>
                    @endcan
                    @can('view stock')
                    <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary w-100 text-start">
                        <i class="fas fa-boxes me-2"></i>View Inventory
                    </a>
                    @endcan
                    @canany(['view sales reports', 'view reports'])
                    <a href="{{ route('reports.sales.index') }}" class="btn btn-outline-secondary w-100 text-start">
                        <i class="fas fa-chart-bar me-2"></i>Sales Reports
                    </a>
                    @endcanany
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
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22, 163, 74, 0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#16a34a',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
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
const paymentCanvas = document.getElementById('paymentChart');
if (paymentCanvas && typeof Chart !== 'undefined') {
    const paymentCtx = paymentCanvas.getContext('2d');
    const paymentValues = @json([$paid, $partial, $unpaid]);
    const hasPaymentData = paymentValues.some(v => v > 0);

    let paymentConfig;

    if (hasPaymentData) {
        paymentConfig = {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Partial', 'Unpaid'],
                datasets: [{
                    data: paymentValues,
                    backgroundColor: [
                        'rgba(34,197,94,0.85)',
                        'rgba(234,179,8,0.85)',
                        'rgba(239,68,68,0.85)'
                    ],
                    borderColor: [
                        'rgba(21,128,61,1)',
                        'rgba(202,138,4,1)',
                        'rgba(185,28,28,1)'
                    ],
                    borderWidth: 2
                }]
            }
        };
    } else {
        // Fallback: show neutral ring when there is no data
        paymentConfig = {
            type: 'doughnut',
            data: {
                labels: ['No data'],
                datasets: [{
                    data: [1],
                    backgroundColor: ['rgba(148,163,184,0.3)'],
                    borderColor: ['rgba(148,163,184,0.6)'],
                    borderWidth: 2
                }]
            }
        };
    }

    new Chart(paymentCtx, {
        ...paymentConfig,
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: {
                    display: false
                },
                // Hide tooltip when we are showing the neutral "no data" ring
                tooltip: {
                    enabled: hasPaymentData
                }
            }
        }
    });
}
</script>
@endpush
