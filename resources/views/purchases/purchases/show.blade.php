@extends('layouts.dashboard')

@section('title', 'Purchase Order Details')
@section('page-title', 'Purchase Order Details')

@section('content')
<div class="row g-3">

    {{-- ── Top Action Bar ── --}}
    <div class="col-12">
        <div class="table-card p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-bold" style="font-size:15px;">{{ $purchase->po_number }}</span>
                @php
                    $statusMap = [
                        'draft'     => ['bg'=>'#f3f4f6','color'=>'#374151','dot'=>'#9ca3af'],
                        'pending'   => ['bg'=>'#fff7ed','color'=>'#c2410c','dot'=>'#f97316'],
                        'ordered'   => ['bg'=>'#eff6ff','color'=>'#1d4ed8','dot'=>'#3b82f6'],
                        'partial'   => ['bg'=>'#fefce8','color'=>'#854d0e','dot'=>'#eab308'],
                        'received'  => ['bg'=>'#f0fdf4','color'=>'#166534','dot'=>'#22c55e'],
                        'cancelled' => ['bg'=>'#fef2f2','color'=>'#991b1b','dot'=>'#ef4444'],
                    ];
                    $sc = $statusMap[$purchase->status] ?? $statusMap['pending'];
                @endphp
                <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;">
                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $sc['dot'] }};display:inline-block;"></span>
                    {{ ucfirst($purchase->status) }}
                </span>
                <span class="text-muted" style="font-size:13px;"><i class="fas fa-calendar me-1"></i>{{ $purchase->order_date->format('d M Y') }}</span>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if($purchase->status !== 'received' && $purchase->status !== 'cancelled')
                    <a href="{{ route('purchases.receive', $purchase) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-check me-1"></i>Receive Items
                    </a>
                    <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                @endif
                @if($purchase->status === 'received')
                    <a href="{{ route('purchase-returns.create', ['purchase_id' => $purchase->id]) }}" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-undo me-1"></i>Return Items
                    </a>
                @endif
                <a href="{{ route('purchases.print', $purchase) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                    <i class="fas fa-print me-1"></i>Print PO
                </a>
                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary">
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
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Total Items</div>
                    <div style="font-size:24px;font-weight:800;color:#111;">{{ $purchase->items->count() }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Total Amount</div>
                    <div style="font-size:20px;font-weight:800;color:#111;">৳{{ number_format($purchase->total_amount, 2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100" style="border-left:3px solid #16a34a;">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Paid</div>
                    <div style="font-size:20px;font-weight:800;color:#16a34a;">৳{{ number_format($purchase->paid_amount, 2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="table-card p-3 text-center h-100" style="border-left:3px solid {{ $purchase->due_amount > 0 ? '#ef4444' : '#16a34a' }};">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;margin-bottom:6px;">Due / Balance</div>
                    <div style="font-size:20px;font-weight:800;color:{{ $purchase->due_amount > 0 ? '#ef4444' : '#16a34a' }};">৳{{ number_format($purchase->due_amount, 2) }}</div>
                    <div class="mt-1">
                        @if($purchase->payment_status === 'paid')
                            <span class="badge bg-success" style="font-size:11px;">✓ Fully Paid</span>
                        @elseif($purchase->payment_status === 'partial')
                            <span class="badge bg-warning text-dark" style="font-size:11px;">Partial</span>
                        @else
                            <span class="badge bg-danger" style="font-size:11px;">Unpaid</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main Content ── --}}
    <div class="col-md-8">
        {{-- PO Info --}}
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-info-circle me-2"></i>Purchase Order Information</h6>
            </div>
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:10px;">Supplier</h6>
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr>
                                <td class="text-muted ps-0" style="width:40%;">Name</td>
                                <td class="fw-semibold pe-0">{{ $purchase->supplier->name }}</td>
                            </tr>
                            @if($purchase->supplier->company_name)
                            <tr>
                                <td class="text-muted ps-0">Company</td>
                                <td class="pe-0">{{ $purchase->supplier->company_name }}</td>
                            </tr>
                            @endif
                            @if($purchase->supplier->phone)
                            <tr>
                                <td class="text-muted ps-0">Phone</td>
                                <td class="pe-0">
                                    <a href="tel:{{ $purchase->supplier->phone }}" style="color:inherit;text-decoration:none;">
                                        <i class="fas fa-phone fa-xs me-1 text-muted"></i>{{ $purchase->supplier->phone }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:10px;">Dates & Payment</h6>
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr>
                                <td class="text-muted ps-0" style="width:45%;">Order Date</td>
                                <td class="pe-0">{{ $purchase->order_date->format('d M Y') }}</td>
                            </tr>
                            @if($purchase->expected_delivery_date)
                            <tr>
                                <td class="text-muted ps-0">Expected Delivery</td>
                                <td class="pe-0">{{ $purchase->expected_delivery_date->format('d M Y') }}</td>
                            </tr>
                            @endif
                            @if($purchase->received_date)
                            <tr>
                                <td class="text-muted ps-0">Received</td>
                                <td class="pe-0 text-success fw-semibold">{{ $purchase->received_date->format('d M Y') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted ps-0">Payment Method</td>
                                <td class="pe-0">
                                    <span class="badge" style="background:#f3f4f6;color:#374151;font-weight:600;">
                                        {{ ucfirst(str_replace('_', ' ', $purchase->payment_method ?? 'cash')) }}
                                    </span>
                                </td>
                            </tr>
                            @if($purchase->bankAccount)
                            <tr>
                                <td class="text-muted ps-0">Bank Account</td>
                                <td class="pe-0">{{ $purchase->bankAccount->account_name }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                @if($purchase->notes || $purchase->internal_notes)
                <hr class="my-3">
                <div class="row">
                    @if($purchase->notes)
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:6px;">Notes</h6>
                        <p class="mb-0 text-muted" style="font-size:14px;">{{ $purchase->notes }}</p>
                    </div>
                    @endif
                    @if($purchase->internal_notes)
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:6px;">Internal Notes</h6>
                        <p class="mb-0 text-muted" style="font-size:14px;">{{ $purchase->internal_notes }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Items --}}
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-list me-2"></i>Purchase Items <span class="badge bg-primary ms-1">{{ $purchase->items->count() }}</span></h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Barcode</th>
                            <th>Serial No.</th>
                            <th class="text-end">Cost</th>
                            <th class="text-end">Selling</th>
                            <th class="text-center">Status</th>
                            <th>Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchase->items as $i => $item)
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
                            <td>{{ $item->serial_number ?? '—' }}</td>
                            <td class="text-end fw-semibold">৳{{ number_format($item->cost_price, 2) }}</td>
                            <td class="text-end text-muted">{{ $item->selling_price ? '৳'.number_format($item->selling_price, 2) : '—' }}</td>
                            <td class="text-center">
                                @php
                                    $itemStatus = [
                                        'received' => 'bg-success',
                                        'damaged'  => 'bg-danger',
                                        'returned' => 'bg-secondary',
                                        'pending'  => 'bg-warning text-dark',
                                    ][$item->status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $itemStatus }}">{{ ucfirst($item->status) }}</span>
                            </td>
                            <td style="font-size:12px;">{{ $item->received_date ? $item->received_date->format('d M Y') : '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No items found.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background:#f9fafb;">
                        <tr>
                            <td colspan="4" class="text-end fw-semibold" style="font-size:13px;">Subtotal</td>
                            <td class="text-end fw-bold" colspan="4">৳{{ number_format($purchase->subtotal, 2) }}</td>
                        </tr>
                        @if($purchase->tax_amount > 0)
                        <tr>
                            <td colspan="4" class="text-end text-muted" style="font-size:13px;">Tax</td>
                            <td class="text-end" colspan="4">+৳{{ number_format($purchase->tax_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($purchase->discount_amount > 0)
                        <tr>
                            <td colspan="4" class="text-end text-muted" style="font-size:13px;">Discount</td>
                            <td class="text-end text-success" colspan="4">-৳{{ number_format($purchase->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr style="border-top:2px solid #e5e7eb;">
                            <td colspan="4" class="text-end fw-bold">Grand Total</td>
                            <td class="text-end fw-bold" colspan="4" style="font-size:16px;">৳{{ number_format($purchase->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Sidebar: Invoice Summary + Returns ── --}}
    <div class="col-md-4">
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-receipt me-2"></i>Payment Summary</h6>
            </div>
            <div class="p-3">
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:13px;color:#6b7280;">Total Amount</span>
                    <span style="font-size:14px;font-weight:700;">৳{{ number_format($purchase->total_amount, 2) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:13px;color:#6b7280;">Paid to Supplier</span>
                    <span style="font-size:14px;font-weight:700;color:#16a34a;">৳{{ number_format($purchase->paid_amount, 2) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:12px;background:{{ $purchase->due_amount > 0 ? '#fef2f2' : '#f0fdf4' }};margin:0 -12px;border-radius:0 0 8px 8px;">
                    <span style="font-size:13px;font-weight:700;color:{{ $purchase->due_amount > 0 ? '#991b1b' : '#166534' }};">Outstanding Due</span>
                    <span style="font-size:16px;font-weight:800;color:{{ $purchase->due_amount > 0 ? '#ef4444' : '#16a34a' }};">৳{{ number_format($purchase->due_amount, 2) }}</span>
                </div>
            </div>
        </div>

        @if($purchase->returns->count() > 0)
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-undo me-2"></i>Returns ({{ $purchase->returns->count() }})</h6>
            </div>
            <div class="p-3">
                @foreach($purchase->returns as $return)
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
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($purchase->status !== 'received')
        <div class="table-card">
            <div class="table-card-header">
                <h6 class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h6>
            </div>
            <div class="p-3">
                <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" onsubmit="return confirm('Delete this purchase order? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="fas fa-trash me-2"></i>Delete Purchase Order
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
