@extends('layouts.dashboard')

@section('title', 'Edit Sale / Invoice')
@section('page-title', 'Edit Sale / Invoice')

@section('content')
<div class="sale-form-wrap">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h5 class="mb-1 fw-bold">Edit Sale / Invoice</h5>
            <p class="text-muted small mb-0"><code class="bg-light px-2 py-1 rounded">{{ $sale->invoice_number }}</code> · {{ $sale->sale_date->format('d M Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-eye me-1"></i>View</a>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
            <button type="submit" form="saleEditForm" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Save</button>
        </div>
    </div>

    <form action="{{ route('sales.update', $sale) }}" method="POST" id="saleEditForm">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="table-card mb-0">
                    <div class="table-card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-user me-2 text-primary"></i>Customer & name on invoice</h6>
                    </div>
                    <div class="p-4 pt-3">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Customer</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control form-control-sm" id="customerSearch" placeholder="Search customer..." autocomplete="off"
                                           value="{{ old('customer_id') ? ($customers->firstWhere('id', old('customer_id', $sale->customer_id))?->name ?? '') : ($sale->customer ? $sale->customer->name : '') }}">
                                    <div class="position-absolute w-100 bg-white border rounded shadow-lg" id="customerDropdown" style="display:none;max-height:260px;overflow-y:auto;z-index:1000;"></div>
                                    <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id', $sale->customer_id) }}">
                                </div>
                                <div class="form-check form-check-sm mt-1">
                                    <input class="form-check-input" type="checkbox" id="walkInCustomer" onchange="toggleWalkIn()" {{ !$sale->customer_id ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="walkInCustomer">Walk-in customer</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="customer_name" class="form-label fw-semibold small">Name on invoice</label>
                                <input type="text" class="form-control form-control-sm" id="customer_name" name="customer_name" placeholder="Name (e.g. for pykari)"
                                       value="{{ old('customer_name', $sale->customer_name) }}">
                                <div class="form-text small">Shown on invoice; can differ from customer.</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="customer_phone" class="form-label fw-semibold small">Phone</label>
                                <input type="text" class="form-control form-control-sm" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $sale->customer_phone) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="customer_address" class="form-label fw-semibold small">Address</label>
                                <input type="text" class="form-control form-control-sm" id="customer_address" name="customer_address" value="{{ old('customer_address', $sale->customer_address) }}">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="sale_date" class="form-label fw-semibold">Sale date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-sm" name="sale_date" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="due_date" class="form-label fw-semibold small">Due date</label>
                                <input type="date" class="form-control form-control-sm" name="due_date" value="{{ old('due_date', $sale->due_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Notes</label>
                                <textarea class="form-control form-control-sm" name="notes" rows="2" placeholder="Customer notes...">{{ old('notes', $sale->notes) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Internal notes</label>
                                <textarea class="form-control form-control-sm" name="internal_notes" rows="2" placeholder="Internal...">{{ old('internal_notes', $sale->internal_notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-card mt-4">
                    <div class="table-card-header bg-light border-0 py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-list me-2 text-primary"></i>Items <span class="badge bg-secondary ms-1">{{ $sale->items->count() }}</span></h6>
                        <span class="badge bg-warning text-dark" style="font-size:11px;"><i class="fas fa-lock me-1"></i>Locked</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>#</th><th>Product</th><th>Barcode</th><th class="text-end">Qty</th><th class="text-end">Unit price</th><th class="text-end">Subtotal</th></tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $i => $item)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td><strong>{{ $item->product->name }}</strong></td>
                                    <td><code class="small">{{ $item->barcode ?? 'N/A' }}</code></td>
                                    <td class="text-end">{{ $item->quantity }}</td>
                                    <td class="text-end">৳{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">৳{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr><td colspan="5" class="text-end fw-semibold">Subtotal</td><td class="text-end fw-bold">৳{{ number_format($sale->subtotal, 2) }}</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="table-card mb-4">
                    <div class="table-card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-calculator me-2 text-primary"></i>Summary & payment</h6>
                    </div>
                    <div class="p-4 pt-3">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Tax (৳)</label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', $sale->tax_amount) }}" oninput="recalculate()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Discount (৳)</label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', $sale->discount_amount) }}" oninput="recalculate()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Paid (৳)</label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="paid_amount" id="paid_amount" value="{{ old('paid_amount', $sale->paid_amount) }}" oninput="recalculate()">
                            <div class="d-flex gap-1 mt-1 flex-wrap">
                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setFullPayment()">Full</button>
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
                            <select class="form-select form-select-sm" name="payment_method" id="payment_method" required onchange="toggleBankAccount()">
                                @php $pm = old('payment_method', $sale->payment_method ?? 'cash'); @endphp
                                <option value="cash" {{ $pm=='cash'?'selected':'' }}>Cash</option>
                                <option value="card" {{ $pm=='card'?'selected':'' }}>Card</option>
                                <option value="mobile_banking" {{ $pm=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                                <option value="bank_transfer" {{ $pm=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                                <option value="cheque" {{ $pm=='cheque'?'selected':'' }}>Cheque</option>
                                <option value="other" {{ $pm=='other'?'selected':'' }}>Other</option>
                            </select>
                        </div>
                        <div class="mb-0" id="bankAccountField" style="display:none;">
                            <label class="form-label small fw-semibold">Bank account</label>
                            <select class="form-select form-select-sm" name="bank_account_id">
                                <option value="">Select account</option>
                                @foreach($bankAccounts as $ba)
                                    <option value="{{ $ba->id }}" {{ old('bank_account_id', $sale->bank_account_id)==$ba->id?'selected':'' }}>{{ $ba->account_name }} — {{ $ba->bank_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@php
$customersJson = $customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'phone' => $c->phone ?? $c->mobile ?? '', 'address' => $c->address ?? ''])->values();
@endphp
@push('scripts')
<script>
const saleSubtotal = {{ $sale->subtotal }};
const customersEdit = @json($customersJson);

function initCustomerSearch() {
    const searchInput = document.getElementById('customerSearch');
    const dropdown = document.getElementById('customerDropdown');
    const customerIdInput = document.getElementById('customer_id');
    if (!searchInput || !dropdown) return;
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';
        if (document.getElementById('walkInCustomer').checked) { dropdown.style.display = 'none'; return; }
        if (query.length < 1) { dropdown.style.display = 'none'; return; }
        const filtered = customersEdit.filter(c => (c.name && c.name.toLowerCase().includes(query)) || (c.phone && c.phone.includes(query)));
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="p-2 text-muted">No customers found. Use Walk-in.</div>';
        } else {
            filtered.slice(0, 12).forEach(customer => {
                const item = document.createElement('div');
                item.className = 'p-2 border-bottom'; item.style.cursor = 'pointer';
                item.innerHTML = '<strong>' + customer.name + '</strong>' + (customer.phone ? ' <small class="text-muted">(' + customer.phone + ')</small>' : '');
                item.addEventListener('click', function() {
                    customerIdInput.value = customer.id;
                    searchInput.value = customer.name;
                    dropdown.style.display = 'none';
                    document.getElementById('walkInCustomer').checked = false;
                    toggleWalkIn();
                    document.getElementById('customer_name').value = customer.name || '';
                    document.getElementById('customer_phone').value = customer.phone || '';
                    document.getElementById('customer_address').value = customer.address || '';
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
function toggleWalkIn() {
    const walkIn = document.getElementById('walkInCustomer').checked;
    if (walkIn) {
        document.getElementById('customer_id').value = '';
        document.getElementById('customerSearch').value = '';
    }
}

function recalculate() {
    const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const paid = parseFloat(document.getElementById('paid_amount').value) || 0;
    const total = saleSubtotal + tax - discount;
    const due = Math.max(0, total - paid);
    document.getElementById('preview_total').textContent = '৳' + total.toFixed(2);
    document.getElementById('preview_paid').textContent = '৳' + paid.toFixed(2);
    document.getElementById('preview_due').textContent = '৳' + due.toFixed(2);
    document.getElementById('preview_due').style.color = due > 0 ? '#ef4444' : '#16a34a';
}
function setFullPayment() {
    const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const total = saleSubtotal + tax - discount;
    document.getElementById('paid_amount').value = total.toFixed(2);
    recalculate();
}
function setZeroPayment() {
    document.getElementById('paid_amount').value = '0.00';
    recalculate();
}
function toggleBankAccount() {
    const method = document.getElementById('payment_method').value;
    document.getElementById('bankAccountField').style.display = ['card','mobile_banking','bank_transfer','cheque'].includes(method) ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    initCustomerSearch();
    recalculate();
    toggleBankAccount();
});
</script>
@endpush
@endsection
