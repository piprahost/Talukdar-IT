@extends('layouts.dashboard')

@section('title', 'Invoice ' . $sale->invoice_number)
@section('page-title', 'Invoice ' . $sale->invoice_number)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Invoices</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $sale->invoice_number }}</li>
@endsection

@section('content')
<div class="row g-3">

    {{-- ── Top Action Bar ── --}}
    <div class="col-12">
        <div class="table-card p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-bold" style="font-size:15px;">{{ $sale->invoice_number }}</span>
                @php
                    $statusMap = [
                        'draft'     => ['bg'=>'#fff7ed','color'=>'#c2410c','dot'=>'#f97316','label'=>'Draft'],
                        'completed' => ['bg'=>'#f0fdf4','color'=>'#166534','dot'=>'#22c55e','label'=>'Completed'],
                        'cancelled' => ['bg'=>'#fef2f2','color'=>'#991b1b','dot'=>'#ef4444','label'=>'Cancelled'],
                        'returned'  => ['bg'=>'#f5f3ff','color'=>'#5b21b6','dot'=>'#8b5cf6','label'=>'Returned'],
                    ];
                    $sc = $statusMap[$sale->status] ?? ['bg'=>'#f3f4f6','color'=>'#374151','dot'=>'#9ca3af','label'=>ucfirst($sale->status)];
                @endphp
                <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;">
                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $sc['dot'] }};display:inline-block;"></span>
                    {{ $sc['label'] }}
                </span>
                @if($sale->sale_date)
                <span class="text-muted" style="font-size:13px;"><i class="fas fa-calendar me-1"></i>{{ $sale->sale_date->format('d M Y') }}</span>
                @endif
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if($sale->status !== 'completed' && $sale->status !== 'cancelled')
                    <form action="{{ route('sales.complete', $sale) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Complete this sale? Stock will be updated and warranty records created.')">
                            <i class="fas fa-check me-1"></i>Complete Sale
                        </button>
                    </form>
                    <a href="{{ route('sales.edit', $sale) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                @endif
                @if($sale->status === 'completed' && $sale->due_amount > 0)
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                        <i class="fas fa-hand-holding-usd me-1"></i>Collect Payment
                    </button>
                @endif
                @if($sale->status === 'completed')
                    <a href="{{ route('sale-returns.create', ['sale_id' => $sale->id]) }}" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-undo me-1"></i>Return Items
                    </a>
                @endif
                <a href="{{ route('sales.print', $sale) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                    <i class="fas fa-print me-1"></i>Print Invoice
                </a>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>
    </div>

    {{-- ── Payment Summary Cards ── --}}
    <div class="col-12">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Subtotal</div>
                    <div style="font-size:20px;font-weight:800;color:#111;">৳{{ number_format($sale->subtotal, 2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Total Amount</div>
                    <div style="font-size:20px;font-weight:800;color:#111;">৳{{ number_format($sale->total_amount, 2) }}</div>
                    @if($sale->tax_amount > 0 || $sale->discount_amount > 0)
                    <div style="font-size:11px;color:#6b7280;margin-top:4px;">
                        @if($sale->tax_amount > 0)<span class="me-2">Tax: ৳{{ number_format($sale->tax_amount, 2) }}</span>@endif
                        @if($sale->discount_amount > 0)<span class="text-success">Disc: -৳{{ number_format($sale->discount_amount, 2) }}</span>@endif
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100" style="border-left:3px solid #16a34a;">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Paid</div>
                    <div style="font-size:20px;font-weight:800;color:#16a34a;">৳{{ number_format($sale->paid_amount, 2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100" style="border-left:3px solid {{ $sale->due_amount > 0 ? '#ef4444' : '#16a34a' }};">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Due / Balance</div>
                    <div style="font-size:20px;font-weight:800;color:{{ $sale->due_amount > 0 ? '#ef4444' : '#16a34a' }};">৳{{ number_format($sale->due_amount, 2) }}</div>
                    <div class="mt-1">
                        @if($sale->payment_status === 'paid')
                            <span class="badge bg-success" style="font-size:11px;">✓ Fully Paid</span>
                        @elseif($sale->payment_status === 'partial')
                            <span class="badge bg-warning text-dark" style="font-size:11px;">Partial</span>
                        @else
                            <span class="badge bg-danger" style="font-size:11px;">Unpaid</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main Detail + Items ── --}}
    <div class="col-md-8">
        {{-- Info card --}}
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-info-circle me-2"></i>Sale Information</h6>
            </div>
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:10px;">Customer</h6>
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr>
                                <td class="text-muted ps-0" style="width:40%;">Name</td>
                                <td class="fw-semibold pe-0">
                                    {{ $sale->customer ? $sale->customer->name : ($sale->customer_name ?? 'Walk-in Customer') }}
                                </td>
                            </tr>
                            @if($sale->customer_phone)
                            <tr>
                                <td class="text-muted ps-0">Phone</td>
                                <td class="pe-0">
                                    <a href="tel:{{ $sale->customer_phone }}" style="color:inherit;text-decoration:none;">
                                        <i class="fas fa-phone fa-xs me-1 text-muted"></i>{{ $sale->customer_phone }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                            @if($sale->customer_address)
                            <tr>
                                <td class="text-muted ps-0 align-top">Address</td>
                                <td class="pe-0">{{ $sale->customer_address }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:10px;">Details</h6>
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr>
                                <td class="text-muted ps-0" style="width:40%;">Sale Date</td>
                                <td class="pe-0">{{ $sale->sale_date->format('d M Y') }}</td>
                            </tr>
                            @if($sale->due_date)
                            <tr>
                                <td class="text-muted ps-0">Due Date</td>
                                <td class="pe-0 {{ $sale->due_date->isPast() && $sale->due_amount > 0 ? 'text-danger fw-semibold' : '' }}">
                                    {{ $sale->due_date->format('d M Y') }}
                                    @if($sale->due_date->isPast() && $sale->due_amount > 0)
                                        <small><i class="fas fa-exclamation-circle ms-1"></i>Overdue</small>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted ps-0">Payment Method</td>
                                <td class="pe-0">
                                    <span class="badge" style="background:#f3f4f6;color:#374151;font-weight:600;">
                                        {{ ucfirst(str_replace('_', ' ', $sale->payment_method ?? 'cash')) }}
                                    </span>
                                </td>
                            </tr>
                            @if($sale->bankAccount)
                            <tr>
                                <td class="text-muted ps-0">Bank Account</td>
                                <td class="pe-0">{{ $sale->bankAccount->account_name }} — {{ $sale->bankAccount->bank_name }}</td>
                            </tr>
                            @endif
                            @if($sale->creator)
                            <tr>
                                <td class="text-muted ps-0">Created By</td>
                                <td class="pe-0">{{ $sale->creator->name }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted ps-0">Created</td>
                                <td class="pe-0">{{ $sale->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                @if($sale->notes || $sale->internal_notes)
                <hr class="my-3">
                <div class="row">
                    @if($sale->notes)
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:6px;">Customer Notes</h6>
                        <p class="mb-0 text-muted" style="font-size:14px;">{{ $sale->notes }}</p>
                    </div>
                    @endif
                    @if($sale->internal_notes)
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:6px;">Internal Notes</h6>
                        <p class="mb-0 text-muted" style="font-size:14px;">{{ $sale->internal_notes }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Sale Items --}}
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-list me-2"></i>Sale Items <span class="badge bg-primary ms-1">{{ $sale->items->count() }}</span></h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Barcode</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Discount</th>
                            <th class="text-center">Warranty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sale->items as $i => $item)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>
                                <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                @if($item->product && $item->product->sku)
                                <br><small class="text-muted">{{ $item->product->sku }}</small>
                                @endif
                            </td>
                            <td>
                                @if($item->barcode)
                                    <code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:11px;">{{ $item->barcode }}</code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $item->quantity }}</span>
                            </td>
                            <td class="text-end">৳{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end {{ $item->discount > 0 ? 'text-success' : 'text-muted' }}">
                                {{ $item->discount > 0 ? '-৳'.number_format($item->discount, 2) : '—' }}
                            </td>
                            <td class="text-center">
                                @if($item->product && $item->product->warranty_period > 0)
                                    <span class="badge" style="background:#f0fdf4;color:#166534;">{{ $item->product->warranty_period }}d</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold">৳{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No items found.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background:#f9fafb;">
                        <tr>
                            <td colspan="7" class="text-end fw-semibold" style="font-size:13px;">Subtotal</td>
                            <td class="text-end fw-bold">৳{{ number_format($sale->subtotal, 2) }}</td>
                        </tr>
                        @if($sale->tax_amount > 0)
                        <tr>
                            <td colspan="7" class="text-end text-muted" style="font-size:13px;">Tax</td>
                            <td class="text-end">+৳{{ number_format($sale->tax_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($sale->discount_amount > 0)
                        <tr>
                            <td colspan="7" class="text-end text-muted" style="font-size:13px;">Discount</td>
                            <td class="text-end text-success">-৳{{ number_format($sale->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr style="border-top:2px solid #e5e7eb;">
                            <td colspan="7" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold" style="font-size:16px;">৳{{ number_format($sale->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Sidebar: Invoice Summary + Returns + Actions ── --}}
    <div class="col-md-4">

        {{-- Invoice Summary --}}
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-receipt me-2"></i>Invoice Summary</h6>
            </div>
            <div class="p-3">
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:13px;color:#6b7280;">Total Amount</span>
                    <span style="font-size:14px;font-weight:700;">৳{{ number_format($sale->total_amount, 2) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:13px;color:#6b7280;">Paid</span>
                    <span style="font-size:14px;font-weight:700;color:#16a34a;">৳{{ number_format($sale->paid_amount, 2) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:12px;background:{{ $sale->due_amount > 0 ? '#fef2f2' : '#f0fdf4' }};margin:0 -12px;border-radius:0 0 8px 8px;">
                    <span style="font-size:13px;font-weight:700;color:{{ $sale->due_amount > 0 ? '#991b1b' : '#166534' }};">Balance Due</span>
                    <span style="font-size:16px;font-weight:800;color:{{ $sale->due_amount > 0 ? '#ef4444' : '#16a34a' }};">৳{{ number_format($sale->due_amount, 2) }}</span>
                </div>
                @if($sale->status === 'completed' && $sale->due_amount > 0)
                <div class="mt-3">
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                        <i class="fas fa-hand-holding-usd me-2"></i>Collect ৳{{ number_format($sale->due_amount, 2) }} Due
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- Payment history (when payments are recorded via Collect Payment) --}}
        @if($sale->payments && $sale->payments->count() > 0)
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-money-bill-wave me-2"></i>Payment History ({{ $sale->payments->count() }})</h6>
                <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary">View in Payments</a>
            </div>
            <div class="p-3">
                @foreach($sale->payments->sortByDesc('payment_date') as $payment)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background:#f9fafb;border-radius:8px;">
                    <div>
                        <div style="font-size:12px;font-weight:700;">{{ $payment->payment_number }}</div>
                        <div style="font-size:11px;color:#6b7280;">{{ $payment->payment_date->format('d M Y') }} · {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'cash')) }}</div>
                    </div>
                    <div class="text-end">
                        <span style="font-size:14px;font-weight:700;color:#16a34a;">৳{{ number_format($payment->amount, 2) }}</span>
                        <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-link p-0 ms-1" title="View payment">View</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Returns --}}
        @if($sale->returns && $sale->returns->count() > 0)
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-undo me-2"></i>Returns ({{ $sale->returns->count() }})</h6>
                <a href="{{ route('sale-returns.index', ['search' => $sale->invoice_number]) }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="p-3">
                @foreach($sale->returns as $return)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background:#f9fafb;border-radius:8px;">
                    <div>
                        <div style="font-size:12px;font-weight:700;">{{ $return->return_number }}</div>
                        <div style="font-size:11px;color:#6b7280;">{{ $return->return_date->format('d M Y') }}</div>
                    </div>
                    <div class="text-end">
                        @if($return->status === 'completed')
                            <span class="badge bg-success" style="font-size:10px;">Completed</span>
                        @elseif($return->status === 'approved')
                            <span class="badge bg-info" style="font-size:10px;">Approved</span>
                        @else
                            <span class="badge bg-warning text-dark" style="font-size:10px;">Pending</span>
                        @endif
                        @if($return->total_amount > 0)
                        <div style="font-size:11px;color:#ef4444;font-weight:600;">-৳{{ number_format($return->total_amount, 2) }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Danger Zone --}}
        @if($sale->status !== 'completed')
        <div class="table-card">
            <div class="table-card-header">
                <h6 class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h6>
            </div>
            <div class="p-3">
                <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Delete this sale? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="fas fa-trash me-2"></i>Delete Sale
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- ── Collect Payment Modal ── --}}
@if($sale->status === 'completed' && $sale->due_amount > 0)
<div class="modal fade" id="collectPaymentModal" tabindex="-1" aria-labelledby="collectPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#f0fdf4;border-bottom:1px solid #dcfce7;">
                <h5 class="modal-title fw-bold" id="collectPaymentModalLabel">
                    <i class="fas fa-hand-holding-usd me-2 text-success"></i>Collect Payment — {{ $sale->invoice_number }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sales.collect-payment', $sale) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    {{-- Balance strip --}}
                    <div class="d-flex justify-content-between mb-4 p-3" style="background:#fafafa;border-radius:10px;border:1px solid #f3f4f6;">
                        <div class="text-center flex-grow-1">
                            <div style="font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:600;">Invoice Total</div>
                            <div style="font-size:16px;font-weight:800;">৳{{ number_format($sale->total_amount, 2) }}</div>
                        </div>
                        <div style="width:1px;background:#e5e7eb;"></div>
                        <div class="text-center flex-grow-1">
                            <div style="font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:600;">Already Paid</div>
                            <div style="font-size:16px;font-weight:800;color:#16a34a;">৳{{ number_format($sale->paid_amount, 2) }}</div>
                        </div>
                        <div style="width:1px;background:#e5e7eb;"></div>
                        <div class="text-center flex-grow-1">
                            <div style="font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:600;">Balance Due</div>
                            <div style="font-size:16px;font-weight:800;color:#ef4444;">৳{{ number_format($sale->due_amount, 2) }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Amount (BDT) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">৳</span>
                            <input type="number" step="0.01" min="0.01" max="{{ $sale->due_amount }}"
                                   class="form-control @error('payment_amount') is-invalid @enderror"
                                   name="payment_amount" id="sale_payment_amount"
                                   value="{{ old('payment_amount', $sale->due_amount) }}"
                                   required oninput="updateSaleAfterPayment()">
                            @error('payment_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setSalePayment({{ $sale->due_amount }})">Full Amount</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setSalePayment({{ $sale->due_amount / 2 }})">Half</button>
                        </div>
                        <div class="mt-2 p-2 rounded" style="background:#f0fdf4;font-size:13px;" id="saleAfterPaymentPreview">
                            <span class="text-muted">Remaining after payment: </span>
                            <strong class="text-success" id="saleRemainingAfter">৳0.00</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror"
                                name="payment_method" id="sale_payment_method" required onchange="toggleSaleBankAccount()">
                            @php $pm = $sale->payment_method ?? 'cash'; @endphp
                            <option value="cash"           {{ $pm=='cash'           ?'selected':'' }}>Cash</option>
                            <option value="card"           {{ $pm=='card'           ?'selected':'' }}>Card</option>
                            <option value="mobile_banking" {{ $pm=='mobile_banking' ?'selected':'' }}>Mobile Banking</option>
                            <option value="bank_transfer"  {{ $pm=='bank_transfer'  ?'selected':'' }}>Bank Transfer</option>
                            <option value="cheque"         {{ $pm=='cheque'         ?'selected':'' }}>Cheque</option>
                            <option value="other"          {{ $pm=='other'          ?'selected':'' }}>Other</option>
                        </select>
                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-0" id="saleBankAccountField" style="display:none;">
                        <label class="form-label fw-semibold">Bank Account</label>
                        <select class="form-select" name="bank_account_id">
                            <option value="">Select Bank Account</option>
                            @foreach(\App\Models\BankAccount::active()->orderBy('account_name')->get() as $ba)
                                <option value="{{ $ba->id }}" {{ $sale->bank_account_id == $ba->id ? 'selected' : '' }}>
                                    {{ $ba->account_name }} — {{ $ba->bank_name }}
                                </option>
                            @endforeach
                        </select>
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
@endif

@push('scripts')
<script>
const saleDueAmount = {{ $sale->due_amount }};

function updateSaleAfterPayment() {
    const entered = parseFloat(document.getElementById('sale_payment_amount').value) || 0;
    const remaining = Math.max(0, saleDueAmount - entered);
    document.getElementById('saleRemainingAfter').textContent = '৳' + remaining.toFixed(2);
    const preview = document.getElementById('saleAfterPaymentPreview');
    if (remaining === 0) {
        preview.style.background = '#f0fdf4';
        document.getElementById('saleRemainingAfter').style.color = '#16a34a';
    } else {
        preview.style.background = '#fef2f2';
        document.getElementById('saleRemainingAfter').style.color = '#ef4444';
    }
}

function setSalePayment(amount) {
    document.getElementById('sale_payment_amount').value = amount.toFixed(2);
    updateSaleAfterPayment();
}

function toggleSaleBankAccount() {
    const method = document.getElementById('sale_payment_method').value;
    const field  = document.getElementById('saleBankAccountField');
    field.style.display = ['card','mobile_banking','bank_transfer','cheque'].includes(method) ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    updateSaleAfterPayment();
    toggleSaleBankAccount();

    @if($errors->has('payment_amount') || $errors->has('payment_method'))
        var modal = new bootstrap.Modal(document.getElementById('collectPaymentModal'));
        modal.show();
    @endif

    if (window.location.hash === '#collectPayment' && document.getElementById('collectPaymentModal')) {
        new bootstrap.Modal(document.getElementById('collectPaymentModal')).show();
    }
});
</script>
@endpush
@endsection
