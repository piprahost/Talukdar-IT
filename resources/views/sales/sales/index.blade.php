@extends('layouts.dashboard')

@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item active" aria-current="page">Invoices</li>
@endsection

@section('content')

{{-- Summary Stats --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-2">
        <div class="table-card p-3 text-center h-100">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:4px;">Total Invoices</div>
            <div style="font-size:24px;font-weight:800;color:#111;">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="table-card p-3 text-center h-100" style="border-left:3px solid #16a34a;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:4px;">Completed</div>
            <div style="font-size:24px;font-weight:800;color:#16a34a;">{{ $stats['completed'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="table-card p-3 text-center h-100" style="border-left:3px solid #f97316;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:4px;">Drafts</div>
            <div style="font-size:24px;font-weight:800;color:#f97316;">{{ $stats['draft'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="table-card p-3 text-center h-100" style="border-left:3px solid #16a34a;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:4px;">Total Revenue</div>
            <div style="font-size:16px;font-weight:800;color:#16a34a;">৳{{ number_format($stats['total_revenue'], 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="table-card p-3 text-center h-100" style="border-left:3px solid #ef4444;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:4px;">Total Due</div>
            <div style="font-size:16px;font-weight:800;color:#ef4444;">৳{{ number_format($stats['total_due'], 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="table-card p-3 text-center h-100" style="border-left:3px solid #ef4444;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:4px;">Unpaid Invoices</div>
            <div style="font-size:24px;font-weight:800;color:#ef4444;">{{ $stats['unpaid_count'] }}</div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-file-invoice-dollar me-2"></i>All Invoices</h6>
        @can('create sales')
        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>New Invoice
        </a>
        @endcan
    </div>

    <div class="filter-wrapper">
        <form method="GET" id="salesFilterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-0">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Invoice # or customer...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-0">Status</label>
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="completed" {{ request('status')=='completed' ?'selected':'' }}>Completed</option>
                        <option value="draft" {{ request('status')=='draft' ?'selected':'' }}>Draft</option>
                        <option value="cancelled" {{ request('status')=='cancelled' ?'selected':'' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-0">Payment</label>
                    <select class="form-select form-select-sm" name="payment_status" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="unpaid" {{ request('payment_status')=='unpaid' ?'selected':'' }}>Unpaid</option>
                        <option value="partial" {{ request('payment_status')=='partial' ?'selected':'' }}>Partial</option>
                        <option value="paid" {{ request('payment_status')=='paid' ?'selected':'' }}>Paid</option>
                    </select>
                </div>
                <div class="col-auto">
                    @if(request()->anyFilled(['search','customer_id','status','payment_status']))
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                    <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                </div>
            </div>
        </form>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr class="table-row-clickable" data-href="{{ route('sales.show', $sale) }}">
                        <td><strong>{{ $sale->invoice_number }}</strong></td>
                        <td>{{ $sale->display_customer_name === 'Walk-in Customer' ? 'Walk-in' : $sale->display_customer_name }}</td>
                        <td>{{ $sale->sale_date->format('d M Y') }}</td>
                        <td><span class="badge bg-secondary">{{ $sale->items()->count() }}</span></td>
                        <td><strong>৳{{ number_format($sale->total_amount, 2) }}</strong></td>
                        <td>
                            @if(($sale->completed_returns_count ?? 0) > 0)
                                <span class="badge bg-info text-dark">Adjusted (Return)</span>
                                @if($sale->due_amount > 0)
                                    <div class="small text-danger mt-1">Due ৳{{ number_format($sale->due_amount, 0) }}</div>
                                @endif
                            @elseif($sale->payment_status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($sale->payment_status === 'partial')
                                <span class="badge bg-warning text-dark">Partial</span>
                            @else
                                <span class="badge bg-danger">Due ৳{{ number_format($sale->due_amount, 0) }}</span>
                            @endif
                        </td>
                        <td>
                            @if(($sale->completed_returns_count ?? 0) > 0)
                                <span class="badge bg-info text-dark">Returned</span>
                            @else
                                <span class="badge {{ $sale->status === 'completed' ? 'bg-success' : ($sale->status === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="text-end" onclick="event.stopPropagation();">
                            <div class="btn-group btn-group-sm">
                                @if($sale->status === 'completed' && $sale->due_amount > 0)
                                    <a href="{{ route('sales.show', $sale) }}#collectPayment" class="btn btn-success btn-sm" title="Collect Payment"><i class="fas fa-hand-holding-usd"></i></a>
                                @endif
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('sales.show', $sale) }}"><i class="fas fa-eye me-2"></i>View</a></li>
                                        @if($sale->status !== 'completed' && $sale->status !== 'cancelled')
                                        <li><a class="dropdown-item" href="{{ route('sales.edit', $sale) }}"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                        @endif
                                        <li><a class="dropdown-item" href="{{ route('sales.print', $sale) }}" target="_blank"><i class="fas fa-print me-2"></i>Print</a></li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No sales found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($sales->hasPages())
        <div class="d-flex justify-content-center mt-4 mb-3">
            {{ $sales->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
document.querySelectorAll('.table-row-clickable').forEach(function(row) {
    row.style.cursor = 'pointer';
    row.addEventListener('click', function() {
        var href = row.getAttribute('data-href');
        if (href) window.location.href = href;
    });
});
</script>
@endpush
@endsection

