@extends('layouts.dashboard')

@section('title', 'Quick Sell')
@section('page-title', 'Quick Sell')

@section('content')
<div class="quick-sell-page">
    {{-- Hero scan zone --}}
    <div class="quick-sell-hero">
        <div class="quick-sell-hero-inner">
            <div class="quick-sell-hero-icon"><i class="fas fa-bolt"></i></div>
            <h1 class="quick-sell-hero-title">Quick Sell</h1>
            <p class="quick-sell-hero-sub">Scan or enter barcode to add items</p>
            <div class="quick-sell-scan-wrap">
                <input type="text" class="quick-sell-scan-input" id="barcodeInput" placeholder="Scan barcode..." autofocus autocomplete="off">
                <button type="button" class="quick-sell-scan-btn" id="addByBarcodeBtn" title="Add">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <p class="quick-sell-hint"><kbd>Enter</kbd> to add · <kbd>Ctrl+B</kbd> focus</p>
            <p class="quick-sell-hint mb-0">প্রতিটি বারকোডের কেনা দাম ও সাজেস্টেড বিক্রয় দাম দেখাবে; প্রয়োজনে লাইন অনুযায়ী দাম ঠিক করুন।</p>
        </div>
    </div>

    <div class="quick-sell-body">
        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Cart --}}
                <div class="quick-sell-cart-card">
                    <div class="quick-sell-cart-header">
                        <h2 class="quick-sell-cart-title"><i class="fas fa-shopping-basket me-2"></i>Cart <span class="quick-sell-cart-badge" id="cartCount">0</span></h2>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clearCartBtn" style="display:none;"><i class="fas fa-trash-alt me-1"></i>Clear</button>
                    </div>
                    <div class="quick-sell-cart-list" id="cartList">
                        <div class="quick-sell-cart-empty" id="cartEmpty">
                            <i class="fas fa-barcode fa-3x text-muted mb-3 opacity-50"></i>
                            <p class="text-muted mb-0">No items yet. Scan or enter a barcode above.</p>
                        </div>
                        <div id="cartItems"></div>
                    </div>
                    <div class="quick-sell-cart-footer d-none" id="cartFooter">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Subtotal</span>
                            <strong id="cartSubtotal">৳0.00</strong>
                        </div>
                    </div>
                </div>

                {{-- Optional customer --}}
                <div class="quick-sell-optional-card">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-user text-muted"></i>
                        <span class="small fw-semibold text-muted">Customer (optional)</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm" id="customerSearch" placeholder="Search customer..." autocomplete="off">
                                <div class="position-absolute w-100 bg-white border rounded shadow-lg mt-1" id="customerDropdown" style="display:none;max-height:200px;overflow-y:auto;z-index:100;"></div>
                                <input type="hidden" name="customer_id" id="customer_id" form="quickSellForm" value="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="customer_name" id="customer_name" form="quickSellForm" placeholder="Name on invoice">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" name="customer_phone" id="customer_phone" form="quickSellForm" placeholder="Phone">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <form action="{{ route('quick-sell.store') }}" method="POST" id="quickSellForm">
                    @csrf
                    <div id="quickSellHiddenItems"></div>
                    <div class="quick-sell-summary-card">
                        <div class="quick-sell-summary-row">
                            <span class="text-muted">Total</span>
                            <strong class="quick-sell-total" id="summaryTotal">৳0.00</strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Payment method</label>
                            <select class="form-select form-select-lg" name="payment_method" id="payment_method" onchange="toggleBankField()">
                                @php $pm = $defaultPaymentMethod ?? 'cash'; @endphp
                                <option value="cash" {{ $pm=='cash'?'selected':'' }}>Cash</option>
                                <option value="card" {{ $pm=='card'?'selected':'' }}>Card</option>
                                <option value="mobile_banking" {{ $pm=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                                <option value="bank_transfer" {{ $pm=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                                <option value="cheque" {{ $pm=='cheque'?'selected':'' }}>Cheque</option>
                                <option value="other" {{ $pm=='other'?'selected':'' }}>Other</option>
                            </select>
                        </div>
                        <div class="mb-4" id="bankField" style="display:none;">
                            <label class="form-label small fw-semibold">Bank account</label>
                            <select class="form-select" name="bank_account_id" form="quickSellForm">
                                <option value="">Select</option>
                                @foreach($bankAccounts as $ba)
                                    <option value="{{ $ba->id }}">{{ $ba->account_name }} — {{ $ba->bank_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="quick-sell-complete-btn" id="completeBtn" disabled>
                            <i class="fas fa-check-circle me-2"></i>Complete Sale
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.quick-sell-page { min-height: 60vh; }
.quick-sell-hero {
    background: linear-gradient(145deg, #0f766e 0%, #115e59 50%, #134e4a 100%);
    color: #fff;
    padding: 2rem 1.5rem;
    border-radius: var(--radius-lg, 16px);
    margin-bottom: 1.5rem;
    box-shadow: 0 20px 50px rgba(15, 118, 110, 0.35);
}
.quick-sell-hero-inner { max-width: 560px; margin: 0 auto; text-align: center; }
.quick-sell-hero-icon {
    width: 56px; height: 56px;
    background: rgba(255,255,255,0.2);
    border-radius: 16px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.5rem; margin-bottom: 1rem;
}
.quick-sell-hero-title { font-size: 1.75rem; font-weight: 800; margin-bottom: 0.25rem; letter-spacing: -0.02em; }
.quick-sell-hero-sub { font-size: 0.9rem; opacity: 0.9; margin-bottom: 1.25rem; }
.quick-sell-scan-wrap {
    display: flex; gap: 0.5rem; align-items: stretch;
    background: rgba(255,255,255,0.12); border-radius: 14px; padding: 0.5rem;
    border: 1px solid rgba(255,255,255,0.2);
}
.quick-sell-scan-input {
    flex: 1; border: none; background: #fff; color: #0f172a;
    padding: 0.85rem 1.25rem; font-size: 1.1rem; border-radius: 10px;
}
.quick-sell-scan-input::placeholder { color: #94a3b8; }
.quick-sell-scan-input:focus { outline: none; box-shadow: 0 0 0 2px rgba(255,255,255,0.5); }
.quick-sell-scan-btn {
    width: 52px; border: none; background: #10b981; color: #fff;
    border-radius: 10px; font-size: 1.25rem; cursor: pointer;
    transition: background 0.2s, transform 0.15s;
}
.quick-sell-scan-btn:hover { background: #059669; transform: scale(1.03); }
.quick-sell-hint { font-size: 0.75rem; margin-top: 0.75rem; opacity: 0.85; }
.quick-sell-hint kbd { background: rgba(0,0,0,0.25); padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; }
.quick-sell-cart-card {
    background: #fff; border-radius: var(--radius-lg, 16px);
    border: 1px solid var(--border-subtle, #e2e8f0);
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
    overflow: hidden;
}
.quick-sell-cart-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; }
.quick-sell-cart-title { font-size: 1.1rem; font-weight: 700; margin: 0; }
.quick-sell-cart-badge { background: #16a34a; color: #fff; font-size: 0.8rem; padding: 0.2rem 0.6rem; border-radius: 999px; margin-left: 0.5rem; }
.quick-sell-cart-list { min-height: 140px; padding: 1rem 1.25rem; }
.quick-sell-cart-empty { text-align: center; padding: 2rem 1rem; }
.quick-sell-cart-footer { padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9; }
.quick-sell-item {
    display: flex; align-items: center; gap: 1rem; padding: 0.75rem 0;
    border-bottom: 1px solid #f1f5f9;
}
.quick-sell-item:last-child { border-bottom: none; }
.quick-sell-item-name { flex: 1; font-weight: 600; }
.quick-sell-item-qty { width: 3rem; text-align: center; font-weight: 700; color: #16a34a; }
.quick-sell-item-price { font-weight: 600; min-width: 5rem; text-align: right; }
.quick-sell-item-remove { color: #94a3b8; cursor: pointer; padding: 0.25rem; }
.quick-sell-item-remove:hover { color: #dc2626; }
.quick-sell-line {
    display: grid;
    grid-template-columns: 1fr auto auto auto auto auto;
    gap: 0.5rem 0.75rem;
    align-items: center;
    padding: 0.65rem 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.875rem;
}
.quick-sell-line:last-child { border-bottom: none; }
.quick-sell-line-head {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
    font-weight: 700;
    padding: 0.35rem 0 0;
    border-bottom: 1px solid #e2e8f0;
}
.quick-sell-line input[type="number"] {
    width: 5.5rem;
    padding: 0.25rem 0.4rem;
    font-size: 0.85rem;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
}
.quick-sell-line .cost-cell { color: #64748b; font-variant-numeric: tabular-nums; }
@media (max-width: 768px) {
    .quick-sell-line { grid-template-columns: 1fr 1fr; }
    .quick-sell-line-head { display: none; }
}
.quick-sell-optional-card {
    background: #f8fafc; border-radius: var(--radius-md, 12px);
    border: 1px solid #e2e8f0; padding: 1rem 1.25rem; margin-top: 1rem;
}
.quick-sell-summary-card {
    background: linear-gradient(180deg, #f0fdf4 0%, #fff 100%);
    border: 1px solid #bbf7d0; border-radius: var(--radius-lg, 16px);
    padding: 1.5rem; box-shadow: 0 8px 24px rgba(22, 163, 74, 0.12);
}
.quick-sell-summary-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; font-size: 1.1rem; }
.quick-sell-total { font-size: 1.5rem; color: #15803d; }
.quick-sell-complete-btn {
    width: 100%; padding: 1rem 1.5rem; font-size: 1.1rem; font-weight: 700;
    border: none; border-radius: 12px; color: #fff;
    background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
    box-shadow: 0 4px 14px rgba(22, 163, 74, 0.4);
    cursor: pointer; transition: transform 0.15s, box-shadow 0.15s;
}
.quick-sell-complete-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(22, 163, 74, 0.5); color: #fff; }
.quick-sell-complete-btn:disabled { opacity: 0.6; cursor: not-allowed; }
</style>
@endpush

@php
    $customersJson = $customers->map(function ($c) {
        return ['id' => $c->id, 'name' => $c->name, 'phone' => $c->phone ?? $c->mobile ?? '', 'address' => $c->address ?? ''];
    })->values();
@endphp
@push('scripts')
<script>
const productsByBarcodeUrl = '{{ route("sales.products-by-barcode") }}';
const customers = @json($customersJson);

let cartLineUid = 0;
/** Each scan = one line: per-barcode purchase cost + editable sell price */
let cartLines = []; // { uid, product_id, product_name, barcode, purchase_cost, unit_price, discount }

function addByBarcode(barcode) {
    const raw = barcode.trim();
    if (!raw) return;
    fetch(productsByBarcodeUrl + '?barcode=' + encodeURIComponent(raw))
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            if (cartLines.some(l => l.barcode === raw)) {
                alert('Already in cart');
                return;
            }
            const unit = parseFloat(data.suggested_unit_price);
            const unitPrice = !isNaN(unit) ? unit : (parseFloat(data.selling_price) || 0);
            const purchaseCost = data.purchase_unit_cost != null && data.purchase_unit_cost !== ''
                ? parseFloat(data.purchase_unit_cost)
                : null;
            cartLines.push({
                uid: ++cartLineUid,
                product_id: data.id,
                product_name: data.name,
                barcode: raw,
                purchase_cost: purchaseCost != null && !isNaN(purchaseCost) ? purchaseCost : null,
                unit_price: unitPrice,
                discount: 0
            });
            document.getElementById('barcodeInput').value = '';
            document.getElementById('barcodeInput').focus();
            renderCart();
        })
        .catch(() => alert('Product not found'));
}

function removeCartLine(uid) {
    cartLines = cartLines.filter(l => l.uid !== uid);
    renderCart();
}

function updateCartLinePrice(uid, val) {
    const line = cartLines.find(l => l.uid === uid);
    if (line) {
        line.unit_price = parseFloat(val) || 0;
        renderCart();
    }
}

function updateCartLineDiscount(uid, val) {
    const line = cartLines.find(l => l.uid === uid);
    if (line) {
        line.discount = parseFloat(val) || 0;
        renderCart();
    }
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const empty = document.getElementById('cartEmpty');
    const footer = document.getElementById('cartFooter');
    const clearBtn = document.getElementById('clearCartBtn');
    const countBadge = document.getElementById('cartCount');
    const subtotalEl = document.getElementById('cartSubtotal');
    const totalEl = document.getElementById('summaryTotal');
    const completeBtn = document.getElementById('completeBtn');
    const hiddenContainer = document.getElementById('quickSellHiddenItems');

    let subtotal = 0;
    hiddenContainer.innerHTML = '';

    cartLines.forEach((line, itemIndex) => {
        const lineTotal = line.unit_price - (line.discount || 0);
        subtotal += lineTotal;
        const wrap = document.createElement('div');
        [
            ['product_id', line.product_id],
            ['barcode', line.barcode],
            ['unit_price', line.unit_price],
            ['discount', line.discount || 0],
            ['quantity', 1],
        ].forEach(([key, val]) => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'items[' + itemIndex + '][' + key + ']';
            inp.value = val;
            wrap.appendChild(inp);
        });
        hiddenContainer.appendChild(wrap);
    });

    const totalQty = cartLines.length;

    if (totalQty === 0) {
        empty.style.display = 'block';
        container.innerHTML = '';
        footer.classList.add('d-none');
        clearBtn.style.display = 'none';
        completeBtn.disabled = true;
    } else {
        empty.style.display = 'none';
        const head = `
            <div class="quick-sell-line quick-sell-line-head d-none d-md-grid">
                <span>Product</span>
                <span>Barcode</span>
                <span>Buy (৳)</span>
                <span>Sell (৳)</span>
                <span>Disc</span>
                <span></span>
            </div>`;
        const rows = cartLines.map(line => {
            const costLabel = line.purchase_cost != null ? '৳' + line.purchase_cost.toFixed(2) : '—';
            const lineTotal = (line.unit_price - (line.discount || 0)).toFixed(2);
            return `
                <div class="quick-sell-line">
                    <div><strong>${escapeHtml(line.product_name)}</strong></div>
                    <div><code class="small">${escapeHtml(line.barcode)}</code></div>
                    <div class="cost-cell">${costLabel}</div>
                    <div>
                        <input type="number" step="0.01" min="0" class="js-clear-zero" value="${line.unit_price}"
                            onchange="updateCartLinePrice(${line.uid}, this.value)" aria-label="Sell price">
                    </div>
                    <div>
                        <input type="number" step="0.01" min="0" class="js-clear-zero" data-zero-restore="0" value="${line.discount || 0}"
                            onchange="updateCartLineDiscount(${line.uid}, this.value)" aria-label="Discount">
                    </div>
                    <div class="d-flex align-items-center gap-2 justify-content-end">
                        <strong>৳${lineTotal}</strong>
                        <span class="quick-sell-item-remove" onclick="removeCartLine(${line.uid})" title="Remove"><i class="fas fa-times"></i></span>
                    </div>
                </div>`;
        }).join('');
        container.innerHTML = head + rows;
        footer.classList.remove('d-none');
        clearBtn.style.display = 'block';
        completeBtn.disabled = false;
    }

    countBadge.textContent = totalQty;
    subtotalEl.textContent = '৳' + subtotal.toFixed(2);
    totalEl.textContent = '৳' + subtotal.toFixed(2);
}

function escapeHtml(s) {
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}

function clearCart() {
    cartLines = [];
    document.getElementById('barcodeInput').value = '';
    document.getElementById('barcodeInput').focus();
    renderCart();
}

function toggleBankField() {
    const method = document.getElementById('payment_method').value;
    document.getElementById('bankField').style.display = ['card','mobile_banking','bank_transfer','cheque'].includes(method) ? 'block' : 'none';
}

document.getElementById('addByBarcodeBtn').addEventListener('click', function() {
    addByBarcode(document.getElementById('barcodeInput').value.trim());
});
document.getElementById('barcodeInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        addByBarcode(this.value.trim());
    }
});
document.getElementById('clearCartBtn').addEventListener('click', clearCart);

document.addEventListener('click', function(e) {
    if (!e.target.closest('#customerSearch') && !e.target.closest('#customerDropdown')) {
        document.getElementById('customerDropdown').style.display = 'none';
    }
});

const customerSearch = document.getElementById('customerSearch');
const customerDropdown = document.getElementById('customerDropdown');
customerSearch.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    customerDropdown.innerHTML = '';
    if (q.length < 1) { customerDropdown.style.display = 'none'; return; }
    const filtered = customers.filter(c => (c.name && c.name.toLowerCase().includes(q)) || (c.phone && c.phone.includes(q)));
    if (filtered.length === 0) {
        customerDropdown.innerHTML = '<div class="p-2 text-muted small">No customers found</div>';
    } else {
        filtered.slice(0, 8).forEach(c => {
            const div = document.createElement('div');
            div.className = 'p-2 border-bottom';
            div.style.cursor = 'pointer';
            div.innerHTML = '<strong>' + c.name + '</strong>' + (c.phone ? ' <small class="text-muted">' + c.phone + '</small>' : '');
            div.addEventListener('click', function() {
                document.getElementById('customer_id').value = c.id;
                customerSearch.value = c.name;
                document.getElementById('customer_name').value = c.name;
                document.getElementById('customer_phone').value = c.phone || '';
                customerDropdown.style.display = 'none';
            });
            customerDropdown.appendChild(div);
        });
    }
    customerDropdown.style.display = 'block';
});

document.addEventListener('DOMContentLoaded', function() {
    renderCart();
    toggleBankField();
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            document.getElementById('barcodeInput').focus();
        }
    });
});
</script>
@endpush
@endsection
