@extends('layouts.dashboard')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="reports-wrap">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h5 class="mb-1 fw-bold">Reports</h5>
            <p class="text-muted small mb-0">Analytics and exports by category.</p>
        </div>
    </div>

    @can('view sales-reports')
    <div class="table-card mb-4">
        <div class="table-card-header bg-light border-0 py-3">
            <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-chart-line me-2 text-success"></i>Sales reports</h6>
        </div>
        <div class="p-4">
            <div class="row g-3">
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.sales.index') }}" class="d-block p-3 rounded border text-decoration-none table-card border-start border-3 border-success">
                        <span class="badge bg-success mb-2 small">Daily</span>
                        <div class="fw-semibold">Sales report</div>
                        <small class="text-muted">Revenue & orders</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.sales.by-product') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">By product</div>
                        <small class="text-muted">Product-wise sales</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.sales.by-customer') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">By customer</div>
                        <small class="text-muted">Customer-wise sales</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.sales.top-products') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">Top products</div>
                        <small class="text-muted">Best sellers</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.sales.by-category') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">By category</div>
                        <small class="text-muted">Category breakdown</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.sales.by-brand') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">By brand</div>
                        <small class="text-muted">Brand breakdown</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @can('view purchase-reports')
    <div class="table-card mb-4">
        <div class="table-card-header bg-light border-0 py-3">
            <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-shopping-cart me-2 text-primary"></i>Purchase reports</h6>
        </div>
        <div class="p-4">
            <div class="row g-3">
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.purchases.index') }}" class="d-block p-3 rounded border text-decoration-none table-card border-start border-3 border-primary">
                        <div class="fw-semibold">Purchase report</div>
                        <small class="text-muted">Vendor spend</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.purchases.by-supplier') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">By supplier</div>
                        <small class="text-muted">Supplier-wise</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.purchases.cost-analysis') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">Cost analysis</div>
                        <small class="text-muted">Cost breakdown</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @can('view financial-reports')
    <div class="table-card mb-4">
        <div class="table-card-header bg-light border-0 py-3">
            <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-file-invoice-dollar me-2 text-info"></i>Financial reports</h6>
        </div>
        <div class="p-4">
            <div class="row g-3">
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.financial.profit-loss') }}" class="d-block p-3 rounded border text-decoration-none table-card border-start border-3 border-info">
                        <div class="fw-semibold">P&L summary</div>
                        <small class="text-muted">Profit & loss</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.financial.accounts-receivable') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">Receivable</div>
                        <small class="text-muted">Outstanding receivables</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.financial.accounts-payable') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">Payable</div>
                        <small class="text-muted">Outstanding payables</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.financial.cash-flow') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">Cash flow</div>
                        <small class="text-muted">Inflows & outflows</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.financial.gross-margin-product') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">Margin (product)</div>
                        <small class="text-muted">Product margin</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.financial.gross-margin-category') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">Margin (category)</div>
                        <small class="text-muted">Category margin</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @can('view inventory-reports')
    <div class="table-card mb-4">
        <div class="table-card-header bg-light border-0 py-3">
            <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-boxes me-2 text-warning"></i>Inventory reports</h6>
        </div>
        <div class="p-4">
            <div class="row g-3">
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.inventory.stock-valuation') }}" class="d-block p-3 rounded border text-decoration-none table-card border-start border-3 border-warning">
                        <div class="fw-semibold">Stock valuation</div>
                        <small class="text-muted">Current stock value</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.inventory.slow-moving') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">Slow moving</div>
                        <small class="text-muted">Low movement items</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.inventory.fast-moving') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">Fast moving</div>
                        <small class="text-muted">High movement items</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <a href="{{ route('reports.inventory.stock-turnover') }}" class="d-block p-3 rounded border text-decoration-none table-card">
                        <div class="fw-semibold">Stock turnover</div>
                        <small class="text-muted">Turnover ratio</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection
