@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="dashboard-wrap">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h5 class="mb-1 fw-bold">Dashboard</h5>
            <p class="text-muted small mb-0">Overview of your business · {{ \Carbon\Carbon::today()->format('M d, Y') }}</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="table-card p-3 h-100 border-start border-3 border-success">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Monthly revenue</div>
                <div class="fw-bold fs-5 text-success">৳{{ number_format($monthSales, 2) }}</div>
                <small class="{{ $salesGrowth >= 0 ? 'text-success' : 'text-danger' }}"><i class="fas fa-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }}"></i> {{ number_format(abs($salesGrowth), 1) }}% vs last month</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="table-card p-3 h-100 border-start border-3 border-primary">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Monthly purchases</div>
                <div class="fw-bold fs-5">৳{{ number_format($monthPurchases, 2) }}</div>
                <small class="{{ $purchaseGrowth >= 0 ? 'text-success' : 'text-danger' }}"><i class="fas fa-arrow-{{ $purchaseGrowth >= 0 ? 'up' : 'down' }}"></i> {{ number_format(abs($purchaseGrowth), 1) }}% vs last month</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="table-card p-3 h-100 border-start border-3 border-info">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Monthly profit</div>
                <div class="fw-bold fs-5 {{ $monthProfit >= 0 ? 'text-success' : 'text-danger' }}">৳{{ number_format($monthProfit, 2) }}</div>
                <small class="{{ $profitMargin >= 0 ? 'text-success' : 'text-danger' }}">Margin {{ number_format($profitMargin, 1) }}%</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="table-card p-3 h-100 border-start border-3 border-secondary">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Today's sales</div>
                <div class="fw-bold fs-5">৳{{ number_format($todaySales, 2) }}</div>
                <small class="text-muted">{{ \Carbon\Carbon::today()->format('M d') }}</small>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="table-card p-3 h-100">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Products</div>
                <div class="fw-bold fs-5">{{ number_format($totalProducts) }}</div>
                <small class="text-muted">{{ $activeProducts }} active</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="table-card p-3 h-100 border-start border-3 {{ $lowStockProducts > 0 ? 'border-danger' : 'border-success' }}">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Low stock</div>
                <div class="fw-bold fs-5 {{ $lowStockProducts > 0 ? 'text-danger' : 'text-success' }}">{{ $lowStockProducts }}</div>
                @if($lowStockProducts > 0)<a href="{{ route('stock.low-stock') }}" class="btn btn-sm btn-outline-danger mt-1 py-0">View</a>@else<small class="text-success">OK</small>@endif
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="table-card p-3 h-100 border-start border-3 border-primary">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Customers</div>
                <div class="fw-bold fs-5">{{ number_format($totalCustomers) }}</div>
                <small class="text-muted">৳{{ number_format($customerDues, 0) }} due</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="table-card p-3 h-100 border-start border-3 border-info">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Suppliers</div>
                <div class="fw-bold fs-5">{{ number_format($totalSuppliers) }}</div>
                <small class="text-muted">৳{{ number_format($supplierDues, 0) }} due</small>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="table-card p-3 h-100 border-start border-3 border-warning">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Pending services</div>
                <div class="fw-bold fs-5 text-warning">{{ $pendingServices }}</div>
                @if($pendingServices > 0)<a href="{{ route('services.index', ['status'=>'pending']) }}" class="btn btn-sm btn-outline-warning mt-1 py-0">View</a>@endif
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="table-card p-3 h-100">
                <div class="small text-uppercase text-muted fw-semibold mb-1">Warranties</div>
                <div class="fw-bold fs-5">{{ $pendingWarranties }}</div>
                <small class="text-muted">pending</small>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="table-card">
                <div class="table-card-header bg-light border-0 py-3">
                    <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-chart-line me-2 text-primary"></i>Sales trend (last 30 days)</h6>
                </div>
                <div class="p-4">
                    <canvas id="salesChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            @php
                $paid = $salesPaymentStatus->get('paid')->total ?? 0;
                $partial = $salesPaymentStatus->get('partial')->total ?? 0;
                $unpaid = $salesPaymentStatus->get('unpaid')->total ?? 0;
            @endphp
            <div class="table-card">
                <div class="table-card-header bg-light border-0 py-3">
                    <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-chart-pie me-2 text-primary"></i>Payment status</h6>
                </div>
                <div class="p-4">
                    <canvas id="paymentChart" height="200"></canvas>
                    <div class="mt-3 small">
                        <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted"><i class="fas fa-circle text-success me-1" style="font-size:6px;vertical-align:middle;"></i> Paid</span><strong>৳{{ number_format($paid, 2) }}</strong></div>
                        <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted"><i class="fas fa-circle text-warning me-1" style="font-size:6px;vertical-align:middle;"></i> Partial</span><strong>৳{{ number_format($partial, 2) }}</strong></div>
                        <div class="d-flex justify-content-between py-2"><span class="text-muted"><i class="fas fa-circle text-danger me-1" style="font-size:6px;vertical-align:middle;"></i> Unpaid</span><strong>৳{{ number_format($unpaid, 2) }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="table-card">
                <div class="table-card-header bg-light border-0 py-3">
                    <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-star me-2 text-primary"></i>Top selling products (last 30 days)</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Product</th><th>Qty</th><th>Revenue</th></tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts->take(5) as $product)
                                <tr>
                                    <td><strong>{{ $product->name }}</strong><br><small class="text-muted">{{ $product->sku }}</small></td>
                                    <td><span class="badge bg-primary">{{ $product->total_qty }}</span></td>
                                    <td class="fw-semibold">৳{{ number_format($product->total_revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No sales data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="table-card">
                <div class="table-card-header bg-light border-0 py-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-clock me-2 text-primary"></i>Recent sales</h6>
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-primary">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Invoice</th><th>Customer</th><th>Amount</th><th>Date</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales->take(7) as $sale)
                                <tr>
                                    <td><a href="{{ route('sales.show', $sale) }}" class="text-decoration-none fw-semibold">{{ $sale->invoice_number }}</a></td>
                                    <td>{{ $sale->customer_name ?? ($sale->customer->name ?? 'Walk-in') }}</td>
                                    <td class="fw-semibold">৳{{ number_format($sale->total_amount, 2) }}</td>
                                    <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                                    <td><span class="badge {{ $sale->payment_status === 'paid' ? 'bg-success' : ($sale->payment_status === 'partial' ? 'bg-warning text-dark' : 'bg-danger') }}">{{ ucfirst($sale->payment_status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No recent sales</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        @if($lowStockProducts > 0 || $customerDues > 0 || $supplierDues > 0 || $pendingServices > 0 || $pendingWarranties > 0)
        <div class="col-xl-8">
            <div class="table-card">
                <div class="table-card-header bg-light border-0 py-3">
                    <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-bell me-2 text-primary"></i>Alerts</h6>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        @if($lowStockProducts > 0)
                        <div class="col-sm-6">
                            <div class="table-card p-3 d-flex align-items-center justify-content-between border-start border-3 border-warning">
                                <div><div class="small text-uppercase text-muted fw-semibold">Low stock</div><div class="fw-bold">{{ $lowStockProducts }} products</div></div>
                                <a href="{{ route('stock.low-stock') }}" class="btn btn-sm btn-outline-warning">View</a>
                            </div>
                        </div>
                        @endif
                        @if($customerDues > 0)
                        <div class="col-sm-6">
                            <div class="table-card p-3 d-flex align-items-center justify-content-between border-start border-3 border-danger">
                                <div><div class="small text-uppercase text-muted fw-semibold">Customer dues</div><div class="fw-bold text-danger">৳{{ number_format($customerDues, 2) }}</div></div>
                                <a href="{{ route('sales.index', ['payment_status'=>'unpaid']) }}" class="btn btn-sm btn-outline-danger">View</a>
                            </div>
                        </div>
                        @endif
                        @if($supplierDues > 0)
                        <div class="col-sm-6">
                            <div class="table-card p-3 d-flex align-items-center justify-content-between border-start border-3 border-primary">
                                <div><div class="small text-uppercase text-muted fw-semibold">Supplier dues</div><div class="fw-bold">৳{{ number_format($supplierDues, 2) }}</div></div>
                                <a href="{{ route('purchases.index', ['payment_status'=>'unpaid']) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </div>
                        </div>
                        @endif
                        @if($pendingServices > 0)
                        <div class="col-sm-6">
                            <div class="table-card p-3 d-flex align-items-center justify-content-between border-start border-3 border-success">
                                <div><div class="small text-uppercase text-muted fw-semibold">Pending services</div><div class="fw-bold">{{ $pendingServices }} orders</div></div>
                                <a href="{{ route('services.index', ['status'=>'pending']) }}" class="btn btn-sm btn-outline-success">View</a>
                            </div>
                        </div>
                        @endif
                        @if($pendingWarranties > 0)
                        <div class="col-sm-6">
                            <div class="table-card p-3 d-flex align-items-center justify-content-between border-start border-3 border-secondary">
                                <div><div class="small text-uppercase text-muted fw-semibold">Warranty submissions</div><div class="fw-bold">{{ $pendingWarranties }} pending</div></div>
                                <a href="{{ route('warranty-submissions.index') }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="col-xl-4">
            <div class="table-card h-100">
                <div class="table-card-header bg-light border-0 py-3">
                    <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-bolt me-2 text-primary"></i>Quick actions</h6>
                </div>
                <div class="p-4 d-flex flex-column gap-2">
                    @can('create sales')
                    <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm w-100 text-start"><i class="fas fa-file-invoice-dollar me-2"></i>New sale / invoice</a>
                    @endcan
                    @can('create purchases')
                    <a href="{{ route('purchases.create') }}" class="btn btn-outline-primary btn-sm w-100 text-start"><i class="fas fa-shopping-cart me-2"></i>New purchase order</a>
                    @endcan
                    @can('create services')
                    <a href="{{ route('services.create') }}" class="btn btn-outline-primary btn-sm w-100 text-start"><i class="fas fa-laptop-medical me-2"></i>New service order</a>
                    @endcan
                    @can('create expenses')
                    <a href="{{ route('expenses.create') }}" class="btn btn-outline-success btn-sm w-100 text-start"><i class="fas fa-receipt me-2"></i>Add expense</a>
                    @endcan
                    @can('view payments')
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm w-100 text-start"><i class="fas fa-money-bill-wave me-2"></i>Payments</a>
                    @endcan
                    @can('view stock')
                    <a href="{{ route('stock.low-stock') }}" class="btn btn-outline-warning btn-sm w-100 text-start"><i class="fas fa-exclamation-triangle me-2"></i>Low stock</a>
                    <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary btn-sm w-100 text-start"><i class="fas fa-warehouse me-2"></i>Stock & barcode</a>
                    @can('create stock')
                    <a href="{{ route('stock.create-manual') }}" class="btn btn-outline-secondary btn-sm w-100 text-start"><i class="fas fa-barcode me-2"></i>Add stock by barcode</a>
                    @endcan
                    @endcan
                    @can('view sales-reports')
                    <a href="{{ route('reports.sales.index') }}" class="btn btn-outline-secondary btn-sm w-100 text-start"><i class="fas fa-chart-bar me-2"></i>Sales report</a>
                    @endcan
                </div>
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
