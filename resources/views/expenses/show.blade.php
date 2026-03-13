@extends('layouts.dashboard')

@section('title', 'Expense Details')
@section('page-title', 'Expense Details')

@section('content')
<div class="row g-3">

    {{-- Action Bar --}}
    <div class="col-12">
        <div class="table-card p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <strong style="font-size:15px;">{{ $expense->expense_number }}</strong>
                @php
                    $statusColors = [
                        'draft'     => ['bg'=>'#fff7ed','color'=>'#c2410c'],
                        'approved'  => ['bg'=>'#eff6ff','color'=>'#1d4ed8'],
                        'paid'      => ['bg'=>'#f0fdf4','color'=>'#166534'],
                        'cancelled' => ['bg'=>'#fef2f2','color'=>'#991b1b'],
                    ][$expense->status] ?? ['bg'=>'#f3f4f6','color'=>'#374151'];
                @endphp
                <span style="background:{{ $statusColors['bg'] }};color:{{ $statusColors['color'] }};padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;">
                    {{ ucfirst($expense->status) }}
                </span>
                <span class="badge" style="background:#f3f4f6;color:#374151;font-size:12px;">{{ $expense->category }}</span>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if(!$expense->isPaid() && !$expense->isCancelled())
                    @if(!$expense->isPaid())
                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    @endif
                    @if($expense->isDraft())
                    <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve this expense?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>Approve</button>
                    </form>
                    @endif
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#markPaidModal">
                        <i class="fas fa-money-check-alt me-1"></i>Mark as Paid
                    </button>
                    <form action="{{ route('expenses.cancel', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this expense?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-times me-1"></i>Cancel</button>
                    </form>
                @endif
                <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>
    </div>

    {{-- Amount Banner --}}
    <div class="col-12">
        <div class="table-card p-4 text-center" style="background:linear-gradient(135deg,#fef2f2,#fee2e2);border-left:5px solid #ef4444;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin-bottom:8px;">Expense Amount</div>
            <div style="font-size:44px;font-weight:800;color:#ef4444;">৳{{ number_format($expense->amount, 2) }}</div>
            <div style="font-size:14px;color:#6b7280;margin-top:4px;">
                {{ $expense->expense_date->format('d M Y') }} · {{ $expense->category }}
                @if($expense->payment_date) · Paid {{ $expense->payment_date->format('d M Y') }} @endif
            </div>
        </div>
    </div>

    {{-- Details --}}
    <div class="col-md-8">
        <div class="table-card mb-3">
            <div class="table-card-header"><h6><i class="fas fa-info-circle me-2"></i>Expense Details</h6></div>
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin-bottom:10px;">Expense Info</h6>
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr><td class="text-muted ps-0" style="width:45%;">Expense #</td><td class="pe-0"><strong>{{ $expense->expense_number }}</strong></td></tr>
                            <tr><td class="text-muted ps-0">Date</td><td class="pe-0">{{ $expense->expense_date->format('d M Y') }}</td></tr>
                            <tr><td class="text-muted ps-0">Category</td><td class="pe-0"><span class="badge" style="background:#f3f4f6;color:#374151;">{{ $expense->category }}</span></td></tr>
                            <tr><td class="text-muted ps-0">Created By</td><td class="pe-0">{{ $expense->creator->name ?? '—' }}</td></tr>
                            @if($expense->approver)
                            <tr><td class="text-muted ps-0">Approved By</td><td class="pe-0">{{ $expense->approver->name }} <small class="text-muted">({{ $expense->approved_at->format('d M Y') }})</small></td></tr>
                            @endif
                            @if($expense->account)
                            <tr><td class="text-muted ps-0">Account</td><td class="pe-0">{{ $expense->account->code }} - {{ $expense->account->name }}</td></tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin-bottom:10px;">Payment & Vendor</h6>
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr><td class="text-muted ps-0" style="width:45%;">Payment Method</td><td class="pe-0"><span class="badge" style="background:#f3f4f6;color:#374151;font-weight:600;">{!! $expense->payment_method_badge !!}</span></td></tr>
                            @if($expense->bankAccount)
                            <tr><td class="text-muted ps-0">Bank Account</td><td class="pe-0">{{ $expense->bankAccount->account_name }}</td></tr>
                            @endif
                            @if($expense->vendor_name)
                            <tr><td class="text-muted ps-0">Vendor</td><td class="pe-0">{{ $expense->vendor_name }}</td></tr>
                            @endif
                            @if($expense->vendor_contact)
                            <tr><td class="text-muted ps-0">Contact</td><td class="pe-0">{{ $expense->vendor_contact }}</td></tr>
                            @endif
                            @if($expense->reference_number)
                            <tr><td class="text-muted ps-0">Reference</td><td class="pe-0"><code style="font-size:12px;">{{ $expense->reference_number }}</code></td></tr>
                            @endif
                            @if($expense->payment_date)
                            <tr><td class="text-muted ps-0">Payment Date</td><td class="pe-0">{{ $expense->payment_date->format('d M Y') }}</td></tr>
                            @endif
                            @if($expense->attachment)
                            <tr>
                                <td class="text-muted ps-0">Attachment</td>
                                <td class="pe-0">
                                    <a href="{{ asset('storage/'.$expense->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-paperclip me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                <hr class="my-3">
                <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin-bottom:6px;">Description</h6>
                <p class="mb-0" style="font-size:14px;">{{ $expense->description }}</p>
                @if($expense->notes)
                <hr class="my-3">
                <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin-bottom:6px;">Additional Notes</h6>
                <p class="mb-0 text-muted" style="font-size:13px;">{{ $expense->notes }}</p>
                @endif
            </div>
        </div>

        {{-- Journal Entry --}}
        @if($journalEntry)
        <div class="table-card">
            <div class="table-card-header"><h6><i class="fas fa-book-open me-2"></i>Accounting Entry</h6></div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Entry #</th>
                            <th>Account</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journalEntry->items as $item)
                        <tr>
                            <td style="font-size:12px;">{{ $journalEntry->entry_number }}</td>
                            <td style="font-size:13px;">{{ $item->account->code }} - {{ $item->account->name }}</td>
                            <td class="text-end" style="color:#ef4444;font-size:13px;">{{ $item->debit > 0 ? '৳'.number_format($item->debit,2) : '—' }}</td>
                            <td class="text-end" style="color:#16a34a;font-size:13px;">{{ $item->credit > 0 ? '৳'.number_format($item->credit,2) : '—' }}</td>
                            <td style="font-size:12px;color:#9ca3af;">{{ $item->description }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="col-md-4">
        <div class="table-card">
            <div class="table-card-header"><h6><i class="fas fa-receipt me-2"></i>Expense Summary</h6></div>
            <div class="p-3">
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;font-size:13px;">
                    <span class="text-muted">Amount</span>
                    <span class="fw-bold text-danger">৳{{ number_format($expense->amount, 2) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;font-size:13px;">
                    <span class="text-muted">Date</span>
                    <span class="fw-semibold">{{ $expense->expense_date->format('d M Y') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;font-size:13px;">
                    <span class="text-muted">Category</span>
                    <span>{{ $expense->category }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:12px;background:{{ $expense->isPaid() ? '#f0fdf4' : '#fff7ed' }};margin:0 -12px;border-radius:0 0 8px 8px;">
                    <span style="font-weight:700;color:{{ $expense->isPaid() ? '#166534' : '#c2410c' }};">Status</span>
                    <span style="font-weight:800;color:{{ $expense->isPaid() ? '#16a34a' : '#f97316' }};">{{ ucfirst($expense->status) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Mark as Paid Modal --}}
@if(!$expense->isPaid() && !$expense->isCancelled())
<div class="modal fade" id="markPaidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#f0fdf4;border-bottom:1px solid #dcfce7;">
                <h5 class="modal-title fw-bold"><i class="fas fa-money-check-alt me-2 text-success"></i>Mark as Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('expenses.mark-paid', $expense) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="p-3 mb-3 rounded" style="background:#fafafa;border:1px solid #e5e7eb;">
                        <div style="font-size:12px;color:#6b7280;">Expense</div>
                        <div class="fw-bold">{{ $expense->expense_number }}</div>
                        <div style="font-size:22px;font-weight:800;color:#ef4444;margin-top:4px;">৳{{ number_format($expense->amount, 2) }}</div>
                    </div>
                    <div>
                        <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-2"></i>Confirm Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
