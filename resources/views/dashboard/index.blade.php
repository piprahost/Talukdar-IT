@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php
    $paid = $salesPaymentStatus->get('paid')?->total ?? 0;
    $partial = $salesPaymentStatus->get('partial')?->total ?? 0;
    $unpaid = $salesPaymentStatus->get('unpaid')?->total ?? 0;
    $hasAlerts = $lowStockProducts > 0 || $customerDues > 0 || $supplierDues > 0 || $pendingServices > 0 || $pendingWarranties > 0;
@endphp
<div class="dashboard-wrap">
    {{-- Hero: welcome + primary number --}}
    <div class="dashboard-hero rounded-3 mb-3 p-3 p-md-4" style="background: linear-gradient(135deg, #0f766e 0%, #134e4a 100%); color: #fff;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1 class="h4 mb-1 fw-bold opacity-90">Dashboard</h1>
                <p class="mb-0 small opacity-75">{{ \Carbon\Carbon::today()->format('l, F j, Y') }}</p>
            </div>
            <div class="text-md-end">
                <div class="small text-uppercase opacity-75" style="letter-spacing:0.05em;">This month</div>
                <div class="h3 mb-0 fw-bold">৳{{ number_format($monthSales, 0) }}</div>
                <div class="small opacity-75">Revenue · {{ $salesGrowth >= 0 ? '+' : '' }}{{ number_format($salesGrowth, 0) }}% vs last month</div>
            </div>
        </div>
    </div>

    {{-- Alerts strip (only when needed) --}}
    @if($hasAlerts)
    <div class="d-flex flex-wrap gap-2 mb-3">
        @if($lowStockProducts > 0)<a href="{{ route('stock.low-stock') }}" class="btn btn-sm btn-light text-dark border"><i class="fas fa-box-open me-1 text-warning"></i>{{ $lowStockProducts }} low stock</a>@endif
        @if($customerDues > 0)<a href="{{ route('sales.index', ['payment_status'=>'unpaid']) }}" class="btn btn-sm btn-light text-dark border"><i class="fas fa-user-clock me-1 text-danger"></i>৳{{ number_format($customerDues, 0) }} receivable</a>@endif
        @if($supplierDues > 0)<a href="{{ route('purchases.index', ['payment_status'=>'unpaid']) }}" class="btn btn-sm btn-light text-dark border"><i class="fas fa-truck me-1 text-primary"></i>৳{{ number_format($supplierDues, 0) }} payable</a>@endif
        @if($pendingServices > 0)<a href="{{ route('services.index', ['status'=>'pending']) }}" class="btn btn-sm btn-light text-dark border"><i class="fas fa-laptop-medical me-1 text-success"></i>{{ $pendingServices }} pending services</a>@endif
        @if($pendingWarranties > 0)<a href="{{ route('warranty-submissions.index') }}" class="btn btn-sm btn-light text-dark border"><i class="fas fa-shield-alt me-1"></i>{{ $pendingWarranties }} warranties</a>@endif
    </div>
    @endif

    {{-- KPI cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="table-card h-100 p-3 border-0 shadow-sm">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Revenue</div>
                <div class="fs-4 fw-bold text-success">৳{{ number_format($monthSales, 0) }}</div>
                <span class="badge {{ $salesGrowth >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-25 text-{{ $salesGrowth >= 0 ? 'success' : 'danger' }} small">{{ $salesGrowth >= 0 ? '+' : '' }}{{ number_format($salesGrowth, 0) }}%</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="table-card h-100 p-3 border-0 shadow-sm">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Purchases</div>
                <div class="fs-4 fw-bold">৳{{ number_format($monthPurchases, 0) }}</div>
                <span class="badge {{ $purchaseGrowth >= 0 ? 'bg-primary' : 'bg-danger' }} bg-opacity-25 text-{{ $purchaseGrowth >= 0 ? 'primary' : 'danger' }} small">{{ $purchaseGrowth >= 0 ? '+' : '' }}{{ number_format($purchaseGrowth, 0) }}%</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="table-card h-100 p-3 border-0 shadow-sm">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Profit</div>
                <div class="fs-4 fw-bold {{ $monthProfit >= 0 ? 'text-success' : 'text-danger' }}">৳{{ number_format($monthProfit, 0) }}</div>
                <span class="badge bg-opacity-25 {{ $profitMargin >= 0 ? 'bg-success text-success' : 'bg-danger text-danger' }} small">{{ number_format($profitMargin, 0) }}% margin</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="table-card h-100 p-3 border-0 shadow-sm">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Today</div>
                <div class="fs-4 fw-bold">৳{{ number_format($todaySales, 0) }}</div>
                <span class="text-muted small">Sales</span>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="table-card border-0 shadow-sm overflow-hidden">
                <div class="table-card-header bg-light border-0 px-4 py-3">
                    <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-chart-area me-2 text-success"></i>Sales (last 30 days)</h6>
                </div>
                <div class="p-4 pt-0">
                    <canvas id="salesChart" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="table-card border-0 shadow-sm h-100">
                <div class="table-card-header bg-light border-0 px-4 py-3">
                    <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-wallet me-2 text-primary"></i>Payment status</h6>
                </div>
                <div class="p-4 d-flex flex-column align-items-center">
                    <div class="position-relative" style="width:180px;height:180px;">
                        <canvas id="paymentChart" width="180" height="180"></canvas>
                    </div>
                    <div class="w-100 mt-3 small">
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom"><span class="text-muted d-flex align-items-center"><span class="rounded-circle me-2 d-inline-block" style="width:8px;height:8px;background:#22c55e;"></span>Paid</span><strong>৳{{ number_format($paid, 0) }}</strong></div>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom"><span class="text-muted d-flex align-items-center"><span class="rounded-circle me-2 d-inline-block" style="width:8px;height:8px;background:#eab308;"></span>Partial</span><strong>৳{{ number_format($partial, 0) }}</strong></div>
                        <div class="d-flex justify-content-between align-items-center py-2"><span class="text-muted d-flex align-items-center"><span class="rounded-circle me-2 d-inline-block" style="width:8px;height:8px;background:#ef4444;"></span>Unpaid</span><strong class="text-danger">৳{{ number_format($unpaid, 0) }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats strip + Tables + Quick actions --}}
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="table-card border-0 shadow-sm p-3">
                <div class="row g-0 text-center">
                    <div class="col-4 col-md-2 border-end"><div class="text-muted small">Products</div><div class="fw-bold">{{ $totalProducts }}</div></div>
                    <div class="col-4 col-md-2 border-end"><div class="text-muted small">Low stock</div><div class="fw-bold {{ $lowStockProducts > 0 ? 'text-danger' : 'text-success' }}">{{ $lowStockProducts }}</div></div>
                    <div class="col-4 col-md-2 border-end"><div class="text-muted small">Customers</div><div class="fw-bold">{{ $totalCustomers }}</div><div class="text-muted small">৳{{ number_format($customerDues, 0) }} due</div></div>
                    <div class="col-4 col-md-2 border-end"><div class="text-muted small">Suppliers</div><div class="fw-bold">{{ $totalSuppliers }}</div></div>
                    <div class="col-4 col-md-2 border-end"><div class="text-muted small">Services</div><div class="fw-bold">{{ $pendingServices }}</div><div class="text-muted small">pending</div></div>
                    <div class="col-4 col-md-2 border-end-0"><div class="text-muted small">Warranties</div><div class="fw-bold">{{ $pendingWarranties }}</div></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="table-card border-0 shadow-sm">
                <div class="table-card-header bg-light border-0 px-4 py-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-fire me-2 text-warning"></i>Top products</h6>
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">Reports</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Product</th><th class="text-end">Qty</th><th class="text-end">Revenue</th></tr></thead>
                        <tbody>
                            @forelse($topProducts->take(5) as $product)
                                <tr>
                                    <td><span class="fw-semibold">{{ \Str::limit($product->name, 28) }}</span></td>
                                    <td class="text-end"><span class="badge bg-primary rounded-pill">{{ $product->total_qty }}</span></td>
                                    <td class="text-end fw-semibold text-success">৳{{ number_format($product->total_revenue, 0) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">No sales data yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="table-card border-0 shadow-sm">
                <div class="table-card-header bg-light border-0 px-4 py-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-receipt me-2 text-primary"></i>Recent sales</h6>
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Invoice</th><th class="text-end">Amount</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($recentSales->take(5) as $sale)
                                <tr>
                                    <td><a href="{{ route('sales.show', $sale) }}" class="text-decoration-none fw-semibold">{{ $sale->invoice_number }}</a></td>
                                    <td class="text-end">৳{{ number_format($sale->total_amount, 0) }}</td>
                                    <td><span class="badge {{ $sale->payment_status === 'paid' ? 'bg-success' : ($sale->payment_status === 'partial' ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill">{{ ucfirst($sale->payment_status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">No sales yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="table-card border-0 shadow-sm h-100">
                <div class="table-card-header bg-light border-0 px-4 py-3">
                    <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-bolt me-2 text-warning"></i>Quick actions</h6>
                </div>
                <div class="p-3 d-flex flex-column gap-2">
                    @can('create sales')<a href="{{ route('sales.create') }}" class="btn btn-success w-100 text-start"><i class="fas fa-plus me-2"></i>New sale</a>@endcan
                    @can('create purchases')<a href="{{ route('purchases.create') }}" class="btn btn-outline-primary w-100 text-start"><i class="fas fa-shopping-cart me-2"></i>New purchase</a>@endcan
                    @can('create services')<a href="{{ route('services.create') }}" class="btn btn-outline-secondary w-100 text-start"><i class="fas fa-laptop-medical me-2"></i>New service</a>@endcan
                    @can('create expenses')<a href="{{ route('expenses.create') }}" class="btn btn-outline-secondary w-100 text-start"><i class="fas fa-receipt me-2"></i>Add expense</a>@endcan
                    @can('view payments')<a href="{{ route('payments.index') }}" class="btn btn-outline-secondary w-100 text-start"><i class="fas fa-money-bill-wave me-2"></i>Payments</a>@endcan
                    @can('view stock')<a href="{{ route('stock.low-stock') }}" class="btn btn-outline-warning w-100 text-start"><i class="fas fa-exclamation-triangle me-2"></i>Low stock</a>@endcan
                    @can('view sales-reports')<a href="{{ route('reports.index') }}" class="btn btn-outline-secondary w-100 text-start"><i class="fas fa-chart-bar me-2"></i>Reports</a>@endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
(function() {
    const salesLabels = @json($salesChartData->pluck('date')->values());
    const salesData = @json($salesChartData->pluck('total')->values());
    const maxVal = Math.max(...salesData, 1);

    const salesCtx = document.getElementById('salesChart');
    if (salesCtx) {
        new Chart(salesCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Sales (৳)',
                    data: salesData,
                    borderColor: '#0f766e',
                    backgroundColor: 'rgba(15, 118, 110, 0.12)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: salesData.every(v => v === 0) ? 0 : 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2,
                    pointBorderColor: '#0f766e'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: { intersect: false, mode: 'index' },
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: maxVal <= 0 ? 1 : undefined,
                        grid: { color: 'rgba(0,0,0,0.06)' },
                        ticks: {
                            maxTicksLimit: 6,
                            callback: function(v) { return '৳' + (v >= 1000 ? (v/1000) + 'k' : v); }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { maxTicksLimit: 10, font: { size: 11 } }
                    }
                }
            }
        });
    }

    const paymentCanvas = document.getElementById('paymentChart');
    if (paymentCanvas && typeof Chart !== 'undefined') {
        const paymentValues = @json([(float)$paid, (float)$partial, (float)$unpaid]);
        const hasPaymentData = paymentValues.some(v => v > 0);
        const ctx = paymentCanvas.getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: hasPaymentData ? {
                labels: ['Paid', 'Partial', 'Unpaid'],
                datasets: [{
                    data: paymentValues,
                    backgroundColor: ['#22c55e', '#eab308', '#ef4444'],
                    borderWidth: 0
                }]
            } : {
                labels: ['No data'],
                datasets: [{ data: [1], backgroundColor: ['#e2e8f0'], borderWidth: 0 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '58%',
                plugins: { legend: { display: false }, tooltip: { enabled: hasPaymentData } }
            }
        });
    }
})();
</script>
@endpush
