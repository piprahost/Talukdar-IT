@extends('layouts.dashboard')

@section('title', 'Expense Details')
@section('page-title', 'Expense Details')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-receipt me-2"></i>Expense: {{ $expense->expense_number }}</h6>
                <div>
                    @if(!$expense->isPaid())
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                    @endif
                    @if($expense->isDraft())
                        <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="d-inline me-2" onsubmit="return confirm('Approve this expense?');">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="fas fa-check me-2"></i>Approve
                            </button>
                        </form>
                    @endif
                    @if(!$expense->isPaid() && !$expense->isCancelled())
                        <button type="button" class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#markPaidModal">
                            <i class="fas fa-money-check-alt me-2"></i>Mark as Paid
                        </button>
                    @endif
                    @if(!$expense->isPaid() && !$expense->isCancelled())
                        <form action="{{ route('expenses.cancel', $expense) }}" method="POST" class="d-inline me-2" onsubmit="return confirm('Cancel this expense?');">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Expense Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 40%;">Expense Number:</th>
                                <td><strong>{{ $expense->expense_number }}</strong></td>
                            </tr>
                            <tr>
                                <th>Expense Date:</th>
                                <td>{{ $expense->expense_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Category:</th>
                                <td><span class="badge bg-secondary">{{ $expense->category }}</span></td>
                            </tr>
                            <tr>
                                <th>Amount:</th>
                                <td><strong class="text-danger">৳{{ number_format($expense->amount, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>{!! $expense->status_badge !!}</td>
                            </tr>
                            @if($expense->payment_date)
                                <tr>
                                    <th>Payment Date:</th>
                                    <td>{{ $expense->payment_date->format('d M Y') }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Created By:</th>
                                <td>{{ $expense->creator->name ?? '-' }}</td>
                            </tr>
                            @if($expense->approver)
                                <tr>
                                    <th>Approved By:</th>
                                    <td>{{ $expense->approver->name }} on {{ $expense->approved_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Payment & Vendor Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 40%;">Payment Method:</th>
                                <td>{!! $expense->payment_method_badge !!}</td>
                            </tr>
                            @if($expense->bankAccount)
                                <tr>
                                    <th>Bank Account:</th>
                                    <td>{{ $expense->bankAccount->account_name }} - {{ $expense->bankAccount->bank_name }}</td>
                                </tr>
                            @endif
                            @if($expense->account)
                                <tr>
                                    <th>Expense Account:</th>
                                    <td>{{ $expense->account->code }} - {{ $expense->account->name }}</td>
                                </tr>
                            @endif
                            @if($expense->vendor_name)
                                <tr>
                                    <th>Vendor Name:</th>
                                    <td>{{ $expense->vendor_name }}</td>
                                </tr>
                            @endif
                            @if($expense->vendor_contact)
                                <tr>
                                    <th>Vendor Contact:</th>
                                    <td>{{ $expense->vendor_contact }}</td>
                                </tr>
                            @endif
                            @if($expense->reference_number)
                                <tr>
                                    <th>Reference Number:</th>
                                    <td>{{ $expense->reference_number }}</td>
                                </tr>
                            @endif
                            @if($expense->attachment)
                                <tr>
                                    <th>Attachment:</th>
                                    <td>
                                        <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-file-pdf me-1"></i>View Attachment
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-2 mb-3">Description</h6>
                        <p class="p-3 bg-light rounded">{{ $expense->description }}</p>
                    </div>
                </div>
                
                @if($expense->notes)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6 class="border-bottom pb-2 mb-3">Additional Notes</h6>
                            <p class="p-3 bg-light rounded">{{ $expense->notes }}</p>
                        </div>
                    </div>
                @endif
                
                @if($journalEntry)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h6 class="border-bottom pb-2 mb-3">Accounting Entry</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Entry Number</th>
                                            <th>Date</th>
                                            <th>Account</th>
                                            <th class="text-end">Debit</th>
                                            <th class="text-end">Credit</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($journalEntry->items as $item)
                                            <tr>
                                                <td>{{ $journalEntry->entry_number }}</td>
                                                <td>{{ $journalEntry->entry_date->format('d M Y') }}</td>
                                                <td>{{ $item->account->code }} - {{ $item->account->name }}</td>
                                                <td class="text-end">{{ $item->debit > 0 ? '৳' . number_format($item->debit, 2) : '-' }}</td>
                                                <td class="text-end">{{ $item->credit > 0 ? '৳' . number_format($item->credit, 2) : '-' }}</td>
                                                <td>{{ $item->description }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markPaidModal" tabindex="-1">
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
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" 
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
@endsection

