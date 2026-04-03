@extends('layouts.dashboard')

@section('title', 'Create Sale / Invoice')
@section('page-title', 'Create Sale / Invoice')

@section('content')
<div class="sale-form-wrap">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h5 class="mb-1 fw-bold">Create Sale / Invoice</h5>
            <p class="text-muted small mb-0">Select customer, add items by barcode, then complete or save as draft.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
            <button type="submit" form="saleForm" name="status" value="draft" class="btn btn-warning btn-sm" id="saveDraftBtn"><i class="fas fa-save me-1"></i>Save draft</button>
            <button type="submit" form="saleForm" name="status" value="completed" class="btn btn-success btn-sm" id="completeBtn" disabled><i class="fas fa-check me-1"></i>Complete sale</button>
        </div>
    </div>

    <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="table-card mb-0">
                    <div class="table-card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-user me-2 text-primary"></i>Customer & date</h6>
                    </div>
                    <div class="p-4 pt-3">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Customer</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control form-control-sm" id="customerSearch" placeholder="Search customer..." autocomplete="off">
                                    <div class="position-absolute w-100 bg-white border rounded shadow-lg" id="customerDropdown" style="display:none;max-height:260px;overflow-y:auto;z-index:1000;"></div>
                                    <input type="hidden" id="customer_id" name="customer_id" value="">
                                </div>
                                <div class="form-check form-check-sm mt-1">
                                    <input class="form-check-input" type="checkbox" id="walkInCustomer" onchange="toggleWalkIn()">
                                    <label class="form-check-label small" for="walkInCustomer">Walk-in customer</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="customer_name" class="form-label fw-semibold small">Name on invoice</label>
                                <input type="text" class="form-control form-control-sm" id="customer_name" name="customer_name" placeholder="Name (e.g. for pykari)">
                                <div class="form-text small">Can differ from customer; used on invoice.</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="customer_phone" class="form-label fw-semibold small">Phone</label>
                                <input type="text" class="form-control form-control-sm" id="customer_phone" name="customer_phone" placeholder="Phone">
                            </div>
                            <div class="col-6 col-md-2">
                                <label for="sale_date" class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-sm" id="sale_date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-6 col-md-2">
                                <label for="due_date" class="form-label fw-semibold small">Due date</label>
                                <input type="date" class="form-control form-control-sm" id="due_date" name="due_date" value="{{ old('due_date', $defaultDueDate ?? null) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-card mt-4">
                    <div class="table-card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-barcode me-2 text-primary"></i>Add items <span class="badge bg-secondary ms-1" id="totalItemsCount">0</span></h6>
                    </div>
                    <div class="p-4 pt-3">
                        <div class="p-3 rounded-3 mb-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label class="form-label small fw-semibold mb-1">Product</label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control form-control-sm" id="productSearch" placeholder="Search or scan barcode..." autocomplete="off">
                                        <div class="position-absolute w-100 bg-white border rounded shadow-lg" id="productDropdown" style="display:none;max-height:260px;overflow-y:auto;z-index:1000;"></div>
                                        <input type="hidden" id="currentProduct" value="">
                                    </div>
                                    <div id="selectedProduct" class="mt-1" style="display:none;">
                                        <span class="small text-success"><strong id="selectedProductName"></strong> <span id="selectedProductStock" class="text-muted"></span></span>
                                        <button type="button" class="btn btn-link btn-sm p-0 ms-1 text-danger" onclick="clearProductSelection()"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                                <div class="col-12 col-md-5">
                                    <label class="form-label small fw-semibold mb-1">Scan barcode <button type="button" class="btn btn-link btn-sm p-0 ms-1" onclick="focusBarcodeScanner()" title="Focus (Ctrl+B)"><i class="fas fa-crosshairs"></i></button></label>
                                    <input type="text" class="form-control form-control-sm" id="barcodeScanner" placeholder="Scan or type" autofocus>
                                </div>
                                <div class="col-12 col-md-3">
                                    <button type="button" class="btn btn-primary btn-sm w-100" onclick="addBarcodeManually()"><i class="fas fa-plus me-1"></i>Add</button>
                                </div>
                            </div>
                            <p class="small text-muted mb-0 mt-2">Each barcode = 1 unit. Scan করলে ওই বারকোডের <strong>কেনা দাম</strong> ও <strong>সাজেস্টেড বিক্রয় দাম</strong> (purchase লাইনের selling price, না থাকলে product price) দেখাবে; লাইন অনুযায়ী বিক্রয় দাম বদলাতে পারবেন।</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0" id="scannedItemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Barcode</th>
                                        <th>Buy (৳)</th>
                                        <th>Qty</th>
                                        <th>Sell (৳)</th>
                                        <th>Discount (৳)</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="scannedItemsBody"></tbody>
                                <tfoot class="table-light">
                                    <tr><th colspan="6" class="text-end">Subtotal</th><th id="itemsSubtotal">৳0.00</th><th></th></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sale-form-sidebar">
                    <div class="table-card mb-4">
                        <div class="table-card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-calculator me-2 text-primary"></i>Summary & payment</h6>
                        </div>
                        <div class="p-4 pt-3">
                            <div class="mb-3">
                                <label for="tax_amount" class="form-label small fw-semibold">Tax (৳)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', 0) }}" oninput="calculateTotals()">
                            </div>
                            <div class="mb-3">
                                <label for="discount_amount" class="form-label small fw-semibold">Discount (৳)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', 0) }}" oninput="calculateTotals()">
                            </div>
                            <div class="mb-3">
                                <label for="paid_amount" class="form-label small fw-semibold">Paid (৳)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" id="paid_amount" name="paid_amount" value="{{ old('paid_amount', 0) }}" oninput="calculateTotals()">
                            </div>
                            <div class="p-3 rounded-3 mb-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Total</span><strong id="totalDisplay">৳0.00</strong></div>
                                <div class="d-flex justify-content-between small"><span class="text-muted">Due</span><strong id="dueDisplay" class="text-danger">৳0.00</strong></div>
                            </div>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label small fw-semibold">Payment method <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required onchange="toggleBankAccount()">
                                    <option value="cash" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='cash'?'selected':'' }}>Cash</option>
                                    <option value="card" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='card'?'selected':'' }}>Card</option>
                                    <option value="mobile_banking" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                                    <option value="bank_transfer" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                                    <option value="cheque" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='cheque'?'selected':'' }}>Cheque</option>
                                    <option value="other" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='other'?'selected':'' }}>Other</option>
                                </select>
                                @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3" id="bankAccountField" style="display:none;">
                                <label for="bank_account_id" class="form-label small fw-semibold">Bank account</label>
                                <select class="form-select form-select-sm @error('bank_account_id') is-invalid @enderror" id="bank_account_id" name="bank_account_id">
                                    <option value="">Select account</option>
                                    @foreach($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}" {{ old('bank_account_id')==$bankAccount->id?'selected':'' }}>{{ $bankAccount->account_name }} — {{ $bankAccount->bank_name }}</option>
                                    @endforeach
                                </select>
                                @error('bank_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label small fw-semibold">Notes</label>
                                <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2" placeholder="Notes...">{{ old('notes') }}</textarea>
                            </div>
                            <div class="mb-0">
                                <label for="internal_notes" class="form-label small fw-semibold">Internal notes</label>
                                <textarea class="form-control form-control-sm" id="internal_notes" name="internal_notes" rows="2" placeholder="Private...">{{ old('internal_notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="d-none d-lg-grid gap-2">
                        <button type="submit" form="saleForm" name="status" value="draft" class="btn btn-warning" id="saveDraftBtnSidebar"><i class="fas fa-save me-2"></i>Save as draft</button>
                        <button type="submit" form="saleForm" name="status" value="completed" class="btn btn-success" id="completeBtnSidebar" disabled><i class="fas fa-check me-2"></i>Complete sale</button>
                        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@php
$customersJson = $customers->map(function($c) {
    return ['id' => $c->id, 'name' => $c->name, 'phone' => $c->phone, 'email' => $c->email];
})->values();

$productsJson = $products->map(function($p) {
    return [
        'id' => $p->id,
        'name' => $p->name,
        'sku' => $p->sku,
        'selling_price' => (float) $p->selling_price,
        'cost_price' => (float) $p->cost_price,
        'stock_quantity' => $p->stock_quantity,
        'barcodes' => $p->barcodes ?? []
    ];
})->values();
@endphp

@push('scripts')
<script>
const customers = @json($customersJson);
const products = @json($productsJson);

let saleLineUid = 0;
/** One row per barcode: purchase cost + editable sell price */
let saleLines = []; // { uid, product_id, product_name, barcode, purchase_cost, unit_price, discount }
let selectedProduct = null;
let selectedCustomer = null;

// Customer Search
function initCustomerSearch() {
    const searchInput = document.getElementById('customerSearch');
    const dropdown = document.getElementById('customerDropdown');
    const customerIdInput = document.getElementById('customer_id');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';
        
        if (query.length < 1) {
            dropdown.style.display = 'none';
            return;
        }
        
        const filtered = customers.filter(c => 
            c.name.toLowerCase().includes(query) || 
            (c.phone && c.phone.includes(query)) ||
            (c.email && c.email.toLowerCase().includes(query))
        );
        
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="p-2 text-muted">No customers found</div>';
        } else {
            filtered.slice(0, 10).forEach(customer => {
                const item = document.createElement('div');
                item.className = 'p-2 cursor-pointer border-bottom';
                item.style.cursor = 'pointer';
                item.innerHTML = `<strong>${customer.name}</strong>${customer.phone ? ' <small class="text-muted">(' + customer.phone + ')</small>' : ''}`;
                item.addEventListener('click', function() {
                    selectCustomer(customer);
                });
                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f0f0f0';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
                dropdown.appendChild(item);
            });
        }
        
        dropdown.style.display = 'block';
    });
    
    function selectCustomer(customer) {
        selectedCustomer = customer;
        customerIdInput.value = customer.id;
        searchInput.value = customer.name;
        dropdown.style.display = 'none';
        document.getElementById('walkInCustomer').checked = false;
        toggleWalkIn();
    }
    
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}

function toggleWalkIn() {
    const walkIn = document.getElementById('walkInCustomer').checked;
    if (walkIn) {
        document.getElementById('customer_id').value = '';
        document.getElementById('customerSearch').value = '';
        selectedCustomer = null;
    }
}

// Product Search
function initProductSearch() {
    const searchInput = document.getElementById('productSearch');
    const dropdown = document.getElementById('productDropdown');
    const productIdInput = document.getElementById('currentProduct');
    const selectedProductDiv = document.getElementById('selectedProduct');
    const selectedProductName = document.getElementById('selectedProductName');
    const selectedProductStock = document.getElementById('selectedProductStock');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';
        
        if (query.length < 1) {
            dropdown.style.display = 'none';
            return;
        }
        
        const filtered = products.filter(p => 
            p.name.toLowerCase().includes(query) || 
            (p.sku && p.sku.toLowerCase().includes(query)) ||
            (p.barcodes && p.barcodes.some(b => b.toLowerCase().includes(query)))
        );
        
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="p-2 text-muted">No products found</div>';
        } else {
            filtered.slice(0, 10).forEach(product => {
                const item = document.createElement('div');
                item.className = 'p-2 cursor-pointer border-bottom';
                item.style.cursor = 'pointer';
                item.innerHTML = `<strong>${product.name}</strong> <small class="text-muted">(${product.sku})</small> <span class="badge bg-info">Stock: ${product.stock_quantity}</span>`;
                item.addEventListener('click', function() {
                    selectProduct(product);
                });
                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f0f0f0';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
                dropdown.appendChild(item);
            });
        }
        
        dropdown.style.display = 'block';
    });
    
    function selectProduct(product) {
        selectedProduct = product;
        productIdInput.value = product.id;
        searchInput.value = '';
        dropdown.style.display = 'none';
        selectedProductName.textContent = `${product.name} (${product.sku})`;
        selectedProductStock.textContent = `Stock: ${product.stock_quantity}`;
        selectedProductDiv.style.display = 'block';
        document.getElementById('barcodeScanner').focus();
    }
    
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}

function clearProductSelection() {
    selectedProduct = null;
    document.getElementById('currentProduct').value = '';
    document.getElementById('productSearch').value = '';
    document.getElementById('selectedProduct').style.display = 'none';
    document.getElementById('productSearch').focus();
}

function focusBarcodeScanner() {
    const scanner = document.getElementById('barcodeScanner');
    scanner.focus();
    scanner.select();
    scanner.style.borderColor = '#10b981';
    setTimeout(() => scanner.style.borderColor = '', 1000);
}

function addBarcodeManually() {
    const scanner = document.getElementById('barcodeScanner');
    const barcode = scanner.value.trim();
    
    if (!barcode) {
        alert('Please enter or scan a barcode');
        return;
    }
    
    processBarcode(barcode);
}

function processBarcode(barcodeRaw) {
    const barcode = String(barcodeRaw).trim();
    if (!barcode) return;

    fetch(`{{ route('sales.products-by-barcode') }}?barcode=${encodeURIComponent(barcode)}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                document.getElementById('barcodeScanner').value = '';
                return;
            }
            const product = products.find(p => p.id === data.id);
            if (!product) {
                alert('Product not found in catalog');
                document.getElementById('barcodeScanner').value = '';
                return;
            }
            if (selectedProduct && selectedProduct.id !== data.id) {
                alert('This barcode belongs to a different product. Clear product selection first.');
                document.getElementById('barcodeScanner').value = '';
                return;
            }
            if (product.barcodes && product.barcodes.length && !product.barcodes.includes(barcode)) {
                alert('This barcode does not belong to the selected product');
                document.getElementById('barcodeScanner').value = '';
                return;
            }
            const stock = typeof data.stock_quantity === 'number' ? data.stock_quantity : product.stock_quantity;
            if (stock <= 0) {
                alert('Product is out of stock');
                document.getElementById('barcodeScanner').value = '';
                return;
            }
            if (saleLines.some(l => l.barcode === barcode)) {
                alert('This barcode is already added');
                document.getElementById('barcodeScanner').value = '';
                return;
            }
            if (!selectedProduct) {
                selectProduct(product);
            }
            const unit = parseFloat(data.suggested_unit_price);
            const unitPrice = !isNaN(unit) ? unit : (parseFloat(product.selling_price) || 0);
            const purchaseCost = data.purchase_unit_cost != null && data.purchase_unit_cost !== ''
                ? parseFloat(data.purchase_unit_cost) : null;
            saleLines.push({
                uid: ++saleLineUid,
                product_id: product.id,
                product_name: product.name,
                barcode,
                purchase_cost: purchaseCost != null && !isNaN(purchaseCost) ? purchaseCost : null,
                unit_price: unitPrice,
                discount: 0
            });
            const scanner = document.getElementById('barcodeScanner');
            scanner.style.backgroundColor = '#d4edda';
            setTimeout(() => {
                scanner.style.backgroundColor = '';
                scanner.value = '';
                scanner.focus();
            }, 300);
            updateScannedItemsTable();
            calculateTotals();
        })
        .catch(() => {
            alert('Product not found for this barcode');
            document.getElementById('barcodeScanner').value = '';
        });
}

function selectProduct(product) {
    selectedProduct = product;
    document.getElementById('currentProduct').value = product.id;
    document.getElementById('productSearch').value = '';
    document.getElementById('productDropdown').style.display = 'none';
    document.getElementById('selectedProductName').textContent = `${product.name} (${product.sku})`;
    document.getElementById('selectedProductStock').textContent = `Stock: ${product.stock_quantity}`;
    document.getElementById('selectedProduct').style.display = 'block';
}

function removeSaleLine(uid) {
    saleLines = saleLines.filter(l => l.uid !== uid);
    updateScannedItemsTable();
    calculateTotals();
}

function updateSaleLinePrice(uid, newPrice) {
    const line = saleLines.find(l => l.uid === uid);
    if (line) {
        line.unit_price = parseFloat(newPrice) || 0;
        updateScannedItemsTable();
        calculateTotals();
    }
}

function updateSaleLineDiscount(uid, newDiscount) {
    const line = saleLines.find(l => l.uid === uid);
    if (line) {
        line.discount = parseFloat(newDiscount) || 0;
        updateScannedItemsTable();
        calculateTotals();
    }
}

function escapeHtmlSale(s) {
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}

function updateScannedItemsTable() {
    const tbody = document.getElementById('scannedItemsBody');
    const totalCount = document.getElementById('totalItemsCount');

    tbody.innerHTML = '';

    let itemIndex = 0;
    let subtotal = 0;

    saleLines.forEach(line => {
        const lineSubtotal = line.unit_price - (line.discount || 0);
        subtotal += lineSubtotal;
        const costCell = line.purchase_cost != null ? '৳' + line.purchase_cost.toFixed(2) : '—';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${escapeHtmlSale(line.product_name)}</strong></td>
            <td><code class="small">${escapeHtmlSale(line.barcode)}</code></td>
            <td class="text-muted small">${costCell}</td>
            <td><strong>1</strong></td>
            <td>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm" value="${line.unit_price}"
                       onchange="updateSaleLinePrice(${line.uid}, this.value)" style="width: 110px;">
            </td>
            <td>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm" value="${line.discount}"
                       onchange="updateSaleLineDiscount(${line.uid}, this.value)" style="width: 90px;">
            </td>
            <td><strong>৳${lineSubtotal.toFixed(2)}</strong></td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSaleLine(${line.uid})" title="Remove">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);

        const hiddenTr = document.createElement('tr');
        hiddenTr.style.display = 'none';
        const wrap = document.createElement('td');
        wrap.colSpan = 8;
        [['product_id', line.product_id], ['barcode', line.barcode], ['unit_price', line.unit_price], ['discount', line.discount || 0], ['quantity', 1]].forEach(([k, v]) => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'items[' + itemIndex + '][' + k + ']';
            inp.value = v;
            wrap.appendChild(inp);
        });
        hiddenTr.appendChild(wrap);
        tbody.appendChild(hiddenTr);
        itemIndex++;
    });

    totalCount.textContent = saleLines.length;
    document.getElementById('itemsSubtotal').textContent = '৳' + subtotal.toFixed(2);

    const hasItems = saleLines.length > 0;
    document.getElementById('completeBtn').disabled = !hasItems;
    const completeSidebar = document.getElementById('completeBtnSidebar');
    if (completeSidebar) completeSidebar.disabled = !hasItems;
}

function toggleBankAccount() {
    const paymentMethod = document.getElementById('payment_method').value;
    const bankAccountField = document.getElementById('bankAccountField');
    
    // Show bank account field for bank transfers, card, mobile banking, and cheque payments
    if (paymentMethod === 'bank_transfer' || paymentMethod === 'card' || paymentMethod === 'mobile_banking' || paymentMethod === 'cheque') {
        bankAccountField.style.display = 'block';
    } else {
        bankAccountField.style.display = 'none';
    }
}

function calculateTotals() {
    let subtotal = 0;
    saleLines.forEach(line => {
        subtotal += line.unit_price - (line.discount || 0);
    });
    
    const taxAmount = parseFloat(document.getElementById('tax_amount')?.value || 0);
    const discountAmount = parseFloat(document.getElementById('discount_amount')?.value || 0);
    const paidAmount = parseFloat(document.getElementById('paid_amount')?.value || 0);
    const total = subtotal + taxAmount - discountAmount;
    const dueAmount = Math.max(0, total - paidAmount);
    
    document.getElementById('totalDisplay').innerHTML = '<strong>৳' + total.toFixed(2) + '</strong>';
    document.getElementById('dueDisplay').innerHTML = '<strong>৳' + dueAmount.toFixed(2) + '</strong>';
}

// Barcode scanning support
let lastKeyTime = Date.now();

document.addEventListener('DOMContentLoaded', function() {
    initCustomerSearch();
    initProductSearch();
    toggleBankAccount();
    
    const scanner = document.getElementById('barcodeScanner');
    
    if (scanner) {
        scanner.addEventListener('keydown', function(e) {
            const currentTime = Date.now();
            if (currentTime - lastKeyTime > 100) {
                // Reset if typing manually
            }
            lastKeyTime = currentTime;
            
            if (e.key === 'Enter' && this.value.trim().length > 0) {
                e.preventDefault();
                processBarcode(this.value.trim());
            }
        });
        
        scanner.addEventListener('paste', function(e) {
            setTimeout(() => {
                if (this.value.trim()) {
                    processBarcode(this.value.trim());
                }
            }, 10);
        });
    }
    
    // Ctrl+B to focus scanner
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            focusBarcodeScanner();
        }
    });
});
</script>
@endpush
@endsection

