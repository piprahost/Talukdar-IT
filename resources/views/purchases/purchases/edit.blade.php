@extends('layouts.dashboard')

@section('title', 'Edit Purchase Order')
@section('page-title', 'Edit Purchase Order')

@section('content')
<style>
.purchase-form-wrap .table-card.mb-0.overflow-visible { overflow: visible !important; }
.purchase-form-wrap .table-card.mb-0.overflow-visible .p-4 { overflow: visible !important; }
</style>
<div class="purchase-form-wrap">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h5 class="mb-1 fw-bold">Edit Purchase Order</h5>
            <p class="text-muted small mb-0"><code class="bg-light px-2 py-1 rounded">{{ $purchase->po_number }}</code> · {{ $purchase->order_date->format('d M Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-eye me-1"></i>View</a>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
            <button type="submit" form="purchaseForm" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Save</button>
        </div>
    </div>

    <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="table-card mb-0 overflow-visible">
                    <div class="table-card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-shopping-bag me-2 text-primary"></i>Order info</h6>
                    </div>
                    <div class="p-4 pt-3 overflow-visible">
                    <div class="row g-3 overflow-visible">
                        <div class="col-md-6 overflow-visible">
                            <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                            <div class="position-relative overflow-visible">
                                <input type="text" class="form-control form-control-sm @error('supplier_id') is-invalid @enderror @error('supplier_name') is-invalid @enderror" id="supplierSearch" placeholder="Search supplier..." autocomplete="off" value="{{ old('supplier_id') ? ($suppliers->firstWhere('id', old('supplier_id', $purchase->supplier_id))?->name ?? '') : old('supplier_name', $purchase->supplier_name) }}">
                                <div class="position-absolute w-100 bg-white border rounded shadow-lg" id="supplierDropdown" style="display:none;max-height:260px;overflow-y:auto;z-index:1000;"></div>
                                <input type="hidden" id="supplier_id" name="supplier_id" value="{{ old('supplier_id', $purchase->supplier_id) }}">
                            </div>
                            <div class="form-check form-check-sm mt-1">
                                <input class="form-check-input" type="checkbox" id="walkInSupplier" onchange="toggleWalkInSupplier()" {{ old('supplier_name', $purchase->supplier_name) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="walkInSupplier">Walk-in supplier</label>
                            </div>
                            <div id="walkInSupplierFields" class="row g-2 mt-2" style="display:{{ old('supplier_name', $purchase->supplier_name) ? 'flex' : 'none' }};">
                                <div class="col-12 col-md-4">
                                    <label for="supplier_name" class="form-label small mb-0">Name</label>
                                    <input type="text" class="form-control form-control-sm" id="supplier_name" name="supplier_name" value="{{ old('supplier_name', $purchase->supplier_name) }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="supplier_phone" class="form-label small mb-0">Phone</label>
                                    <input type="text" class="form-control form-control-sm" id="supplier_phone" name="supplier_phone" value="{{ old('supplier_phone', $purchase->supplier_phone) }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="supplier_address" class="form-label small mb-0">Address</label>
                                    <input type="text" class="form-control form-control-sm" id="supplier_address" name="supplier_address" value="{{ old('supplier_address', $purchase->supplier_address) }}">
                                </div>
                            </div>
                            @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @error('supplier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                            <label class="form-label fw-semibold small text-muted">Notes</label>
                            <textarea class="form-control form-control-sm" name="notes" rows="2" placeholder="Supplier notes...">{{ old('notes', $purchase->notes) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Internal notes</label>
                            <textarea class="form-control form-control-sm" name="internal_notes" rows="2" placeholder="Internal...">{{ old('internal_notes', $purchase->internal_notes) }}</textarea>
                        </div>
                    </div>
                </div>
                </div>

                <div class="table-card mt-4">
                    <div class="table-card-header bg-light border-0 py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-list me-2 text-primary"></i>Items <span class="badge bg-secondary ms-1">{{ $purchase->items->count() }}</span></h6>
                        <span class="badge bg-warning text-dark" style="font-size:11px;"><i class="fas fa-lock me-1"></i>Locked</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>#</th><th>Product</th><th>Barcode</th><th class="text-end">Unit cost</th><th class="text-end">Unit sell</th><th class="text-center">Status</th></tr>
                            </thead>
                            <tbody>
                                @forelse($purchase->items as $i => $item)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td><strong>{{ $item->product->name }}</strong>@if($item->product->sku)<div class="small text-muted">{{ $item->product->sku }}</div>@endif</td>
                                    <td><code class="small">{{ $item->barcode }}</code></td>
                                    <td class="text-end">৳{{ number_format($item->cost_price, 2) }}</td>
                                    <td class="text-end text-muted">{{ $item->selling_price ? '৳'.number_format($item->selling_price,2) : '—' }}</td>
                                    <td class="text-center"><span class="badge {{ $item->status === 'received' ? 'bg-success' : 'bg-warning text-dark' }}" style="font-size:11px;">{{ ucfirst($item->status) }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-3 text-muted">No items.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr><td colspan="3" class="text-end fw-semibold">Subtotal</td><td class="text-end fw-bold">৳{{ number_format($purchase->subtotal, 2) }}</td><td colspan="2"></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="purchase-form-sidebar">
                    <div class="table-card mb-4">
                        <div class="table-card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-calculator me-2 text-primary"></i>Summary & payment</h6>
                        </div>
                        <div class="p-4 pt-3">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Tax (৳)</label>
                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', $purchase->tax_amount) }}" oninput="recalculate()">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Discount (৳)</label>
                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', $purchase->discount_amount) }}" oninput="recalculate()">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Paid (৳) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="paid_amount" id="paid_amount" value="{{ old('paid_amount', $purchase->paid_amount) }}" oninput="recalculate()">
                                <div class="d-flex gap-1 mt-1 flex-wrap">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setFullPayment()">Full</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setHalfPayment()">Half</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setZeroPayment()">None</button>
                                </div>
                            </div>
                            <div class="p-3 rounded-3 mb-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Total</span><strong id="preview_total">৳0.00</strong></div>
                                <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Paid</span><strong class="text-success" id="preview_paid">৳0.00</strong></div>
                                <div class="d-flex justify-content-between small"><span class="text-muted">Due</span><strong id="preview_due" class="text-danger">৳0.00</strong></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Payment method <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm @error('payment_method') is-invalid @enderror" name="payment_method" id="payment_method" required onchange="toggleBankAccount()">
                                    @php $pm = old('payment_method', $purchase->payment_method ?? 'cash'); @endphp
                                    <option value="cash" {{ $pm=='cash'?'selected':'' }}>Cash</option>
                                    <option value="card" {{ $pm=='card'?'selected':'' }}>Card</option>
                                    <option value="mobile_banking" {{ $pm=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                                    <option value="bank_transfer" {{ $pm=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                                    <option value="cheque" {{ $pm=='cheque'?'selected':'' }}>Cheque</option>
                                    <option value="other" {{ $pm=='other'?'selected':'' }}>Other</option>
                                </select>
                                @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-0" id="bankAccountField" style="display:none;">
                                <label class="form-label small fw-semibold">Bank account</label>
                                <select class="form-select form-select-sm @error('bank_account_id') is-invalid @enderror" name="bank_account_id">
                                    <option value="">Select account</option>
                                    @foreach($bankAccounts as $ba)
                                        <option value="{{ $ba->id }}" {{ old('bank_account_id', $purchase->bank_account_id)==$ba->id?'selected':'' }}>{{ $ba->account_name }} — {{ $ba->bank_name }}</option>
                                    @endforeach
                                </select>
                                @error('bank_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="d-none d-lg-grid gap-2">
                        <button type="submit" form="purchaseForm" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save changes</button>
                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@php
$suppliersJsonEdit = $suppliers->map(function($s) {
    return ['id' => $s->id, 'name' => $s->name, 'company_name' => $s->company_name ?? '', 'phone' => $s->phone ?? '', 'email' => $s->email ?? ''];
})->values();
@endphp
@push('scripts')
<script>
const poTotal = {{ $purchase->total_amount }};
const suppliersEdit = @json($suppliersJsonEdit);

function initSupplierSearch() {
    const searchInput = document.getElementById('supplierSearch');
    const dropdown = document.getElementById('supplierDropdown');
    const supplierIdInput = document.getElementById('supplier_id');
    if (!searchInput || !dropdown) return;
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';
        if (document.getElementById('walkInSupplier').checked) { dropdown.style.display = 'none'; return; }
        if (query.length < 1) { dropdown.style.display = 'none'; return; }
        const filtered = suppliersEdit.filter(s => (s.name && s.name.toLowerCase().includes(query)) || (s.company_name && s.company_name.toLowerCase().includes(query)) || (s.phone && s.phone.includes(query)));
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="p-2 text-muted">No suppliers found. Use Walk-in supplier.</div>';
        } else {
            filtered.slice(0, 12).forEach(supplier => {
                const item = document.createElement('div');
                item.className = 'p-2 border-bottom'; item.style.cursor = 'pointer';
                item.innerHTML = '<strong>' + supplier.name + '</strong>' + (supplier.company_name ? ' <small class="text-muted">— ' + supplier.company_name + '</small>' : '');
                item.addEventListener('click', function() {
                    supplierIdInput.value = supplier.id;
                    searchInput.value = supplier.name + (supplier.company_name ? ' — ' + supplier.company_name : '');
                    dropdown.style.display = 'none';
                    document.getElementById('walkInSupplier').checked = false;
                    toggleWalkInSupplier();
                });
                item.addEventListener('mouseenter', function() { this.style.backgroundColor = '#f0f0f0'; });
                item.addEventListener('mouseleave', function() { this.style.backgroundColor = ''; });
                dropdown.appendChild(item);
            });
        }
        dropdown.style.display = 'block';
    });
    document.addEventListener('click', function(e) {
        if (searchInput && !searchInput.contains(e.target) && dropdown && !dropdown.contains(e.target)) dropdown.style.display = 'none';
    });
}
function toggleWalkInSupplier() {
    const walkIn = document.getElementById('walkInSupplier').checked;
    document.getElementById('walkInSupplierFields').style.display = walkIn ? 'flex' : 'none';
    if (walkIn) {
        document.getElementById('supplier_id').value = '';
        document.getElementById('supplierSearch').value = '';
    } else {
        document.getElementById('supplier_name').value = '';
        document.getElementById('supplier_phone').value = '';
        document.getElementById('supplier_address').value = '';
    }
}

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
    initSupplierSearch();
    toggleWalkInSupplier();
    recalculate();
    toggleBankAccount();
});
</script>
@endpush
@endsection
