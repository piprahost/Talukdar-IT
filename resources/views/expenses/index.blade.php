@extends('layouts.dashboard')

@section('title', 'Expenses')
@section('page-title', 'Expenses')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-receipt me-2"></i>Expense Management</h6>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Expense
        </a>
    </div>
    
    <!-- Statistics Cards -->
    <div class="p-4 border-bottom">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Expenses</div>
                        <div class="stat-value">৳{{ number_format($stats['total_amount'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">This Month</div>
                        <div class="stat-value">৳{{ number_format($stats['this_month'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Draft</div>
                        <div class="stat-value">{{ $stats['draft'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Approved</div>
                        <div class="stat-value">{{ $stats['approved'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Paid</div>
                        <div class="stat-value">{{ $stats['paid'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="p-4 border-bottom">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                       placeholder="Search expense number, description, vendor...">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                    <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                    <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Paid</option>
                    <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category')==$category?'selected':'' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="payment_method" onchange="this.form.submit()">
                    <option value="">All Payment Methods</option>
                    <option value="cash" {{ request('payment_method')=='cash'?'selected':'' }}>Cash</option>
                    <option value="card" {{ request('payment_method')=='card'?'selected':'' }}>Card</option>
                    <option value="mobile_banking" {{ request('payment_method')=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                    <option value="bank_transfer" {{ request('payment_method')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                    <option value="cheque" {{ request('payment_method')=='cheque'?'selected':'' }}>Cheque</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="From Date">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
    
    <!-- Expenses Table -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Expense #</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Vendor</th>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td><strong>{{ $expense->expense_number }}</strong></td>
                        <td>{{ $expense->expense_date->format('d M Y') }}</td>
                        <td><span class="badge bg-secondary">{{ $expense->category }}</span></td>
                        <td>{{ $expense->vendor_name ?? '-' }}</td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px;" title="{{ $expense->description }}">
                                {{ $expense->description }}
                            </div>
                        </td>
                        <td class="text-end"><strong>৳{{ number_format($expense->amount, 2) }}</strong></td>
                        <td>{!! $expense->payment_method_badge !!}</td>
                        <td>{!! $expense->status_badge !!}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$expense->isPaid())
                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if($expense->isDraft())
                                    <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve this expense?');">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-success" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                @if(!$expense->isPaid() && !$expense->isCancelled())
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#markPaidModal{{ $expense->id }}" title="Mark as Paid">
                                        <i class="fas fa-money-check-alt"></i>
                                    </button>
                                @endif
                                @if(!$expense->isPaid() && !$expense->isCancelled())
                                    <form action="{{ route('expenses.cancel', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this expense?');">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-danger" title="Cancel">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- Mark as Paid Modal -->
                            <div class="modal fade" id="markPaidModal{{ $expense->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('expenses.mark-paid', $expense) }}" method="POST">
                                            @csrf
                                            @method('POST')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Mark Expense as Paid</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Expense #:</strong> {{ $expense->expense_number }}</p>
                                                <p><strong>Amount:</strong> ৳{{ number_format($expense->amount, 2) }}</p>
                                                <div class="mb-3">
                                                    <label for="payment_date{{ $expense->id }}" class="form-label">Payment Date</label>
                                                    <input type="date" class="form-control" id="payment_date{{ $expense->id }}" 
                                                           name="payment_date" value="{{ date('Y-m-d') }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Mark as Paid</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No expenses found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{ $expenses->links() }}
</div>
@endsection

