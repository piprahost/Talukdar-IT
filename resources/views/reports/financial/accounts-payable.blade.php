@extends('layouts.dashboard')

@section('title', 'Accounts Payable')
@section('page-title', 'Accounts Payable')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-credit-card me-2"></i>Accounts Payable Report</h6>
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
    
    <!-- Summary -->
    <div class="p-4 border-bottom">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="stat-card danger">
                    <div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Payable</div>
                        <div class="stat-value">৳{{ number_format($totalPayable, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card info">
                    <div class="stat-icon"><i class="fas fa-truck"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Suppliers with Dues</div>
                        <div class="stat-value">{{ $suppliers->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Suppliers Table -->
    <div class="table-responsive p-4">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Contact</th>
                    <th class="text-end">Total Due</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td>
                            <a href="{{ route('suppliers.show', $supplier) }}"><strong>{{ $supplier->name }}</strong></a>
                            @if($supplier->company_name)
                                <br><small class="text-muted">{{ $supplier->company_name }}</small>
                            @endif
                        </td>
                        <td>{{ $supplier->phone }}<br><small class="text-muted">{{ $supplier->email }}</small></td>
                        <td class="text-end">
                            <strong class="text-danger">৳{{ number_format($supplier->purchases_sum_due_amount, 2) }}</strong>
                        </td>
                        <td>
                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('payments.create', ['supplier_id' => $supplier->id]) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-money-check-alt"></i> Make Payment
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No outstanding payables.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

