@extends('layouts.dashboard')

@section('title', 'Accounts Receivable')
@section('page-title', 'Accounts Receivable')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-hand-holding-usd me-2"></i>Accounts Receivable Report</h6>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('reports.financial.accounts-receivable', ['export' => 'csv']) }}"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.financial.accounts-receivable', ['export' => 'xlsx']) }}"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                <li><a class="dropdown-item" href="{{ route('reports.financial.accounts-receivable', ['export' => 'pdf']) }}"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
            </ul>
        </div>
    </div>

    <!-- Summary & Aging as module-stat-card style -->
    <div class="p-4 border-bottom">
        <div class="row g-3 module-stats">
            <div class="col-md-6">
                <div class="module-stat-card rounded-3 p-3" style="background:linear-gradient(135deg,#fef2f2,#fee2e2);border-left:4px solid #ef4444;">
                    <div class="small text-muted text-uppercase fw-bold">Total Receivable</div>
                    <div class="fw-bold fs-5" style="color:#991b1b;">৳{{ number_format($totalReceivable, 2) }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="module-stat-card rounded-3 p-3" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-left:4px solid #3b82f6;">
                    <div class="small text-muted text-uppercase fw-bold">Customers with Dues</div>
                    <div class="fw-bold fs-5" style="color:#1d4ed8;">{{ $customers->count() }}</div>
                </div>
            </div>
        </div>
        <div class="row g-2 mt-3">
            <div class="col-md-2">
                <div class="module-stat-card rounded-3 p-3 small" style="background:#f8fafc;border-left:4px solid #3b82f6;"><strong>Current</strong><br>৳{{ number_format($agingAnalysis['current'], 2) }}</div>
            </div>
            <div class="col-md-2">
                <div class="module-stat-card rounded-3 p-3 small" style="background:#f8fafc;border-left:4px solid #eab308;"><strong>1-30 Days</strong><br>৳{{ number_format($agingAnalysis['1_30'], 2) }}</div>
            </div>
            <div class="col-md-2">
                <div class="module-stat-card rounded-3 p-3 small" style="background:#f8fafc;border-left:4px solid #f97316;"><strong>31-60 Days</strong><br>৳{{ number_format($agingAnalysis['31_60'], 2) }}</div>
            </div>
            <div class="col-md-2">
                <div class="module-stat-card rounded-3 p-3 small" style="background:#f8fafc;border-left:4px solid #ea580c;"><strong>61-90 Days</strong><br>৳{{ number_format($agingAnalysis['61_90'], 2) }}</div>
            </div>
            <div class="col-md-2">
                <div class="module-stat-card rounded-3 p-3 small" style="background:#f8fafc;border-left:4px solid #ef4444;"><strong>Over 90 Days</strong><br>৳{{ number_format($agingAnalysis['over_90'], 2) }}</div>
            </div>
        </div>
    </div>
    
    <!-- Customers Table -->
    <div class="table-responsive p-4">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th class="text-end">Total Due</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>
                            <a href="{{ route('customers.show', $customer) }}"><strong>{{ $customer->name }}</strong></a>
                            @if($customer->company_name)
                                <br><small class="text-muted">{{ $customer->company_name }}</small>
                            @endif
                        </td>
                        <td>{{ $customer->phone }}<br><small class="text-muted">{{ $customer->email }}</small></td>
                        <td class="text-end">
                            <strong class="text-danger">৳{{ number_format($customer->sales_sum_due_amount, 2) }}</strong>
                        </td>
                        <td>
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('payments.create', ['customer_id' => $customer->id]) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-money-check-alt"></i> Receive Payment
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No outstanding receivables.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

