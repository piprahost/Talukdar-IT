@extends('layouts.dashboard')

@section('title', 'Edit Purchase Order')
@section('page-title', 'Edit Purchase Order')

@section('content')
<div class="row g-3">
    <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm">
        @csrf
        @method('PUT')

        {{-- ── Left Column: Supplier + Items ── --}}
        <div class="col-md-8">

            {{-- Purchase Order Info --}}
            <div class="table-card mb-3">
                <div class="table-card-header">
                    <div>
                        <h6 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Edit Purchase Order</h6>
                        <small style="color:#9ca3af;">{{ $purchase->po_number }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                        <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select @error('supplier_id') is-invalid @enderror" name="supplier_id" required>
                                <option value="">— Select Supplier —</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchase->supplier_id)==$supplier->id?'selected':'' }}>
                                        {{ $supplier->name }}@if($supplier->company_name) — {{ $supplier->company_name }}@endif
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Order Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('order_date') is-invalid @enderror"
                                   name="order_date" value="{{ old('order_date', $purchase->order_date->format('Y-m-d')) }}" required>
                            @error('order_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Expected Delivery</label>
                            <input type="date" class="form-control @error('expected_delivery_date') is-invalid @enderror"
                                   name="expected_delivery_date"
                                   value="{{ old('expected_delivery_date', $purchase->expected_delivery_date?->format('Y-m-d')) }}">
                            @error('expected_delivery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Supplier notes...">{{ old('notes', $purchase->notes) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Internal Notes</label>
                            <textarea class="form-control" name="internal_notes" rows="2" placeholder="Internal notes...">{{ old('internal_notes', $purchase->internal_notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items (read-only) --}}
            <div class="table-card">
                <div class="table-card-header">
                    <h6><i class="fas fa-list me-2"></i>Purchase Items <span class="badge bg-primary ms-1">{{ $purchase->items->count() }}</span></h6>
                    <span class="badge" style="background:#fff7ed;color:#c2410c;font-size:12px;">
                        <i class="fas fa-lock me-1"></i>Items are locked after creation
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Barcode</th>
                                <th class="text-end">Cost</th>
                                <th class="text-end">Selling</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchase->items as $i => $item)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <strong style="font-size:14px;">{{ $item->product->name }}</strong>
                                    @if($item->product->sku)
                                    <div style="font-size:11px;color:#9ca3af;">{{ $item->product->sku }}</div>
                                    @endif
                                </td>
                                <td><code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:11px;">{{ $item->barcode }}</code></td>
                                <td class="text-end">৳{{ number_format($item->cost_price, 2) }}</td>
                                <td class="text-end text-muted">{{ $item->selling_price ? '৳'.number_format($item->selling_price,2) : '—' }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $item->status === 'received' ? 'bg-success' : 'bg-warning text-dark' }}" style="font-size:11px;">{{ ucfirst($item->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-3 text-muted">No items.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot style="background:#f9fafb;">
                            <tr>
                                <td colspan="3" class="text-end fw-semibold" style="font-size:13px;">Subtotal</td>
                                <td class="text-end fw-bold">৳{{ number_format($purchase->subtotal, 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── Right Column: Financial + Payment ── --}}
        <div class="col-md-4">

            {{-- PO Info Strip --}}
            <div class="alert alert-light border mb-3 py-2 px-3" style="font-size:13px;">
                <i class="fas fa-hashtag me-1 text-muted"></i>
                <strong>{{ $purchase->po_number }}</strong>
                <span class="text-muted ms-2">· {{ $purchase->order_date->format('d M Y') }}</span>
            </div>

            {{-- Financial Summary --}}
            <div class="table-card mb-3">
                <div class="table-card-header">
                    <h6><i class="fas fa-calculator me-2"></i>Financials</h6>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tax Amount (BDT)</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">৳</span>
                            <input type="number" step="0.01" min="0" class="form-control"
                                   name="tax_amount" id="tax_amount"
                                   value="{{ old('tax_amount', $purchase->tax_amount) }}"
                                   oninput="recalculate()">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Discount Amount (BDT)</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">৳</span>
                            <input type="number" step="0.01" min="0" class="form-control"
                                   name="discount_amount" id="discount_amount"
                                   value="{{ old('discount_amount', $purchase->discount_amount) }}"
                                   oninput="recalculate()">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Paid Amount (BDT) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">৳</span>
                            <input type="number" step="0.01" min="0" class="form-control"
                                   name="paid_amount" id="paid_amount"
                                   value="{{ old('paid_amount', $purchase->paid_amount) }}"
                                   oninput="recalculate()">
                        </div>
                        <div class="d-flex gap-2 mt-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setFullPayment()">Full</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setHalfPayment()">Half</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setZeroPayment()">None</button>
                        </div>
                    </div>

                    {{-- Live payment summary --}}
                    <div class="p-3 rounded" style="background:#f9fafb;border:1px solid #e5e7eb;">
                        <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                            <span class="text-muted">Total Amount</span>
                            <strong id="preview_total">৳{{ number_format($purchase->total_amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                            <span class="text-muted">Paid</span>
                            <strong class="text-success" id="preview_paid">৳0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between" style="font-size:13px;">
                            <span class="text-muted">Due</span>
                            <strong class="text-danger" id="preview_due">৳0.00</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="table-card mb-3">
                <div class="table-card-header">
                    <h6><i class="fas fa-credit-card me-2"></i>Payment Method</h6>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror"
                                name="payment_method" id="payment_method" required onchange="toggleBankAccount()">
                            @php $pm = old('payment_method', $purchase->payment_method ?? 'cash'); @endphp
                            <option value="cash"           {{ $pm=='cash'           ?'selected':'' }}>Cash</option>
                            <option value="card"           {{ $pm=='card'           ?'selected':'' }}>Card</option>
                            <option value="mobile_banking" {{ $pm=='mobile_banking' ?'selected':'' }}>Mobile Banking</option>
                            <option value="bank_transfer"  {{ $pm=='bank_transfer'  ?'selected':'' }}>Bank Transfer</option>
                            <option value="cheque"         {{ $pm=='cheque'         ?'selected':'' }}>Cheque</option>
                            <option value="other"          {{ $pm=='other'          ?'selected':'' }}>Other</option>
                        </select>
                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-0" id="bankAccountField" style="display:none;">
                        <label class="form-label fw-semibold">Bank Account <span class="text-danger">*</span></label>
                        <select class="form-select @error('bank_account_id') is-invalid @enderror"
                                name="bank_account_id">
                            <option value="">— Select Bank Account —</option>
                            @foreach($bankAccounts as $ba)
                                <option value="{{ $ba->id }}" {{ old('bank_account_id', $purchase->bank_account_id)==$ba->id?'selected':'' }}>
                                    {{ $ba->account_name }} — {{ $ba->bank_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('bank_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
                <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
const poTotal = {{ $purchase->total_amount }};

function recalculate() {
    const tax      = parseFloat(document.getElementById('tax_amount').value) || 0;
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const paid     = parseFloat(document.getElementById('paid_amount').value) || 0;
    const subtotal = {{ $purchase->subtotal }};
    const total    = subtotal + tax - discount;
    const due      = Math.max(0, total - paid);

    document.getElementById('preview_total').textContent = '৳' + total.toFixed(2);
    document.getElementById('preview_paid').textContent  = '৳' + paid.toFixed(2);
    document.getElementById('preview_due').textContent   = '৳' + due.toFixed(2);
    document.getElementById('preview_due').style.color   = due > 0 ? '#ef4444' : '#16a34a';
}

function setFullPayment() {
    const tax      = parseFloat(document.getElementById('tax_amount').value) || 0;
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const total    = {{ $purchase->subtotal }} + tax - discount;
    document.getElementById('paid_amount').value = total.toFixed(2);
    recalculate();
}
function setHalfPayment() {
    const tax      = parseFloat(document.getElementById('tax_amount').value) || 0;
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const total    = {{ $purchase->subtotal }} + tax - discount;
    document.getElementById('paid_amount').value = (total / 2).toFixed(2);
    recalculate();
}
function setZeroPayment() {
    document.getElementById('paid_amount').value = '0.00';
    recalculate();
}

function toggleBankAccount() {
    const method = document.getElementById('payment_method').value;
    const field  = document.getElementById('bankAccountField');
    field.style.display = ['card','mobile_banking','bank_transfer','cheque'].includes(method) ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    recalculate();
    toggleBankAccount();
});
</script>
@endpush
@endsection
