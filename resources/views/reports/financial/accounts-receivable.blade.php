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
                <li><a class="dropdown-item" href="#"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="p-4 border-bottom">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="stat-card danger">
                    <div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Receivable</div>
                        <div class="stat-value">৳{{ number_format($totalReceivable, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card info">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Customers with Dues</div>
                        <div class="stat-value">{{ $customers->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Aging Analysis -->
        <div class="row mt-3">
            <div class="col-md-12">
                <h6 class="mb-3">Aging Analysis</h6>
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="alert alert-info mb-0">
                            <strong>Current:</strong> ৳{{ number_format($agingAnalysis['current'], 2) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-warning mb-0">
                            <strong>1-30 Days:</strong> ৳{{ number_format($agingAnalysis['1_30'], 2) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-warning mb-0">
                            <strong>31-60 Days:</strong> ৳{{ number_format($agingAnalysis['31_60'], 2) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-danger mb-0">
                            <strong>Over 90 Days:</strong> ৳{{ number_format($agingAnalysis['over_90'], 2) }}
                        </div>
                    </div>
                </div>
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

