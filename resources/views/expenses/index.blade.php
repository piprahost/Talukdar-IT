@extends('layouts.dashboard')

@section('title', 'Expenses')
@section('page-title', 'Expense Management')

@section('content')

{{-- Stats --}}
<div class="module-stats mb-3">
    <div class="module-stat-card" style="border-left:3px solid #ef4444;">
        <div class="msc-label">Total Expenses</div>
        <div class="msc-value" style="font-size:16px;color:#ef4444;">৳{{ number_format($stats['total_amount'], 0) }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #3b82f6;">
        <div class="msc-label">This Month</div>
        <div class="msc-value" style="font-size:16px;color:#3b82f6;">৳{{ number_format($stats['this_month'], 0) }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #f97316;">
        <div class="msc-label">Draft</div>
        <div class="msc-value" style="color:#f97316;">{{ $stats['draft'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #8b5cf6;">
        <div class="msc-label">Approved</div>
        <div class="msc-value" style="color:#8b5cf6;">{{ $stats['approved'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #16a34a;">
        <div class="msc-label">Paid</div>
        <div class="msc-value" style="color:#16a34a;">{{ $stats['paid'] }}</div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-receipt me-2"></i>All Expenses</h6>
        @can('create expenses')
        <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Create Expense
        </a>
        @endcan
    </div>

    <div class="filter-wrapper">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Expense #, description, vendor...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="draft"     {{ request('status')=='draft'     ?'selected':'' }}>Draft</option>
                        <option value="approved"  {{ request('status')=='approved'  ?'selected':'' }}>Approved</option>
                        <option value="paid"      {{ request('status')=='paid'      ?'selected':'' }}>Paid</option>
                        <option value="cancelled" {{ request('status')=='cancelled' ?'selected':'' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="category" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category')==$cat?'selected':'' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}" placeholder="From">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}" placeholder="To">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100"><i class="fas fa-search"></i></button>
                    @if(request()->anyFilled(['search','status','category','date_from','date_to']))
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Expense #</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Vendor</th>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td>
                        <strong style="font-size:13px;">{{ $expense->expense_number }}</strong>
                        @if($expense->reference_number)
                        <div style="font-size:11px;color:#9ca3af;">Ref: {{ $expense->reference_number }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $expense->expense_date->format('d M Y') }}</td>
                    <td>
                        <span class="badge" style="background:#f3f4f6;color:#374151;font-size:11px;">{{ $expense->category }}</span>
                    </td>
                    <td style="font-size:13px;">{{ $expense->vendor_name ?? '—' }}</td>
                    <td>
                        <div class="text-truncate" style="max-width:180px;font-size:13px;" title="{{ $expense->description }}">
                            {{ $expense->description }}
                        </div>
                    </td>
                    <td class="text-end fw-bold">৳{{ number_format($expense->amount, 2) }}</td>
                    <td class="text-center">
                        @php
                            $statusColors = [
                                'draft'     => ['bg'=>'#fff7ed','color'=>'#c2410c'],
                                'approved'  => ['bg'=>'#eff6ff','color'=>'#1d4ed8'],
                                'paid'      => ['bg'=>'#f0fdf4','color'=>'#166534'],
                                'cancelled' => ['bg'=>'#fef2f2','color'=>'#991b1b'],
                            ][$expense->status] ?? ['bg'=>'#f3f4f6','color'=>'#374151'];
                        @endphp
                        <span style="background:{{ $statusColors['bg'] }};color:{{ $statusColors['color'] }};padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;">
                            {{ ucfirst($expense->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center flex-wrap">
                            <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-primary" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(!$expense->isPaid() && !$expense->isCancelled())
                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
                            @if($expense->isDraft())
                            <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve this expense?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endif
                            @if(!$expense->isPaid() && !$expense->isCancelled())
                            <button type="button" class="btn btn-sm btn-success" title="Mark as Paid"
                                    onclick="openMarkPaid({{ $expense->id }}, '{{ $expense->expense_number }}', {{ $expense->amount }})">
                                <i class="fas fa-money-check-alt"></i>
                            </button>
                            <form action="{{ route('expenses.cancel', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this expense?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancel">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-receipt fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">No expenses found.</p>
                        @can('create expenses')
                        <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Create First Expense
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($expenses->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">Showing {{ $expenses->firstItem() }}–{{ $expenses->lastItem() }} of {{ $expenses->total() }} expenses</small>
        {{ $expenses->links() }}
    </div>
    @endif
</div>

{{-- Single Mark as Paid Modal (shared across all rows) --}}
<div class="modal fade" id="markPaidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#f0fdf4;border-bottom:1px solid #dcfce7;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-money-check-alt me-2 text-success"></i>Mark Expense as Paid
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="markPaidForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="p-3 mb-3 rounded" style="background:#fafafa;border:1px solid #e5e7eb;">
                        <div style="font-size:12px;color:#6b7280;">Expense Number</div>
                        <div class="fw-bold" id="modalExpenseNumber"></div>
                        <div style="font-size:12px;color:#6b7280;margin-top:8px;">Amount</div>
                        <div class="fw-bold text-danger" style="font-size:20px;" id="modalExpenseAmount"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="payment_date" id="modalPaymentDate" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Confirm Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openMarkPaid(expenseId, expenseNumber, amount) {
    document.getElementById('modalExpenseNumber').textContent = expenseNumber;
    document.getElementById('modalExpenseAmount').textContent = '৳' + parseFloat(amount).toFixed(2);
    document.getElementById('markPaidForm').action = `/expenses/${expenseId}/mark-paid`;
    document.getElementById('modalPaymentDate').value = '{{ date('Y-m-d') }}';
    new bootstrap.Modal(document.getElementById('markPaidModal')).show();
}
</script>
@endpush
@endsection
