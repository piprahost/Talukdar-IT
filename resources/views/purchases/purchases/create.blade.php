@extends('layouts.dashboard')

@section('title', 'Create Purchase Order')
@section('page-title', 'Create Purchase Order')

@section('content')
<style>
.purchase-form-wrap .table-card.mb-0.overflow-visible { overflow: visible !important; }
.purchase-form-wrap .table-card.mb-0.overflow-visible .p-4 { overflow: visible !important; }
</style>
<div class="purchase-form-wrap">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h5 class="mb-1 fw-bold">Create Purchase Order</h5>
            <p class="text-muted small mb-0">Select supplier, add items by barcode, then set payment.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
            <button type="submit" form="purchaseForm" class="btn btn-primary btn-sm" id="submitBtn" disabled><i class="fas fa-save me-1"></i>Create PO</button>
        </div>
    </div>

    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="table-card mb-0 overflow-visible">
                    <div class="table-card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-info-circle me-2 text-primary"></i>Order info</h6>
                    </div>
                    <div class="p-4 pt-3 overflow-visible">
                        <div class="row g-3 overflow-visible">
                            <div class="col-md-6 overflow-visible">
                                <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                                <div class="position-relative overflow-visible">
                                    <input type="text" class="form-control form-control-sm @error('supplier_id') is-invalid @enderror @error('supplier_name') is-invalid @enderror" id="supplierSearch" placeholder="Search supplier by name, company, phone..." autocomplete="off" value="{{ old('supplier_id') ? ($suppliers->firstWhere('id', old('supplier_id'))?->name ?? '') : old('supplier_name') }}">
                                    <div class="position-absolute w-100 bg-white border rounded shadow-lg" id="supplierDropdown" style="display:none;max-height:260px;overflow-y:auto;z-index:1000;"></div>
                                    <input type="hidden" id="supplier_id" name="supplier_id" value="{{ old('supplier_id') }}">
                                </div>
                                <div class="form-check form-check-sm mt-1">
                                    <input class="form-check-input" type="checkbox" id="walkInSupplier" name="walk_in_supplier" value="1" onchange="toggleWalkInSupplier()" {{ old('supplier_name') ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="walkInSupplier">Walk-in supplier (e.g. used laptop buy)</label>
                                </div>
                                <div id="walkInSupplierFields" class="row g-2 mt-2" style="display:{{ old('supplier_name') ? 'flex' : 'none' }};">
                                    <div class="col-12 col-md-4">
                                        <label for="supplier_name" class="form-label small fw-semibold mb-0">Name</label>
                                        <input type="text" class="form-control form-control-sm" id="supplier_name" name="supplier_name" placeholder="Name" value="{{ old('supplier_name') }}">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label for="supplier_phone" class="form-label small fw-semibold mb-0">Phone</label>
                                        <input type="text" class="form-control form-control-sm" id="supplier_phone" name="supplier_phone" placeholder="Phone" value="{{ old('supplier_phone') }}">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label for="supplier_address" class="form-label small fw-semibold mb-0">Address</label>
                                        <input type="text" class="form-control form-control-sm" id="supplier_address" name="supplier_address" placeholder="Address (optional)" value="{{ old('supplier_address') }}">
                                    </div>
                                </div>
                                @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @error('supplier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @error('supplier_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="order_date" class="form-label fw-semibold">Order date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('order_date') is-invalid @enderror" id="order_date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required>
                                @error('order_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="expected_delivery_date" class="form-label fw-semibold">Expected delivery</label>
                                <input type="date" class="form-control @error('expected_delivery_date') is-invalid @enderror" id="expected_delivery_date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}">
                                @error('expected_delivery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                        <input type="text" class="form-control form-control-sm" id="productSearch" placeholder="Search by name or SKU..." autocomplete="off">
                                        <div class="position-absolute w-100 bg-white border rounded shadow-lg" id="productDropdown" style="display:none;max-height:260px;overflow-y:auto;z-index:1000;"></div>
                                        <input type="hidden" id="currentProduct" value="">
                                    </div>
                                    <div id="selectedProduct" class="mt-1" style="display:none;">
                                        <span class="small text-success"><strong id="selectedProductName"></strong></span>
                                        <button type="button" class="btn btn-link btn-sm p-0 ms-1 text-danger" onclick="clearProductSelection()"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                                <div class="col-12 col-md-5">
                                    <label class="form-label small fw-semibold mb-1">Scan barcode <button type="button" class="btn btn-link btn-sm p-0 ms-1" onclick="focusBarcodeScanner()" title="Focus (Ctrl+B)"><i class="fas fa-crosshairs"></i></button></label>
                                    <input type="text" class="form-control form-control-sm" id="barcodeScanner" placeholder="Scan or type barcode" autofocus>
                                </div>
                                <div class="col-12 col-md-3">
                                    <button type="button" class="btn btn-primary btn-sm w-100" onclick="addBarcodeManually()"><i class="fas fa-plus me-1"></i>Add</button>
                                </div>
                            </div>
                            <p class="small text-muted mb-0 mt-2">Select product, then scan. Each barcode = 1 unit. <strong>Unit cost</strong> is price <em>per piece</em>; line total = unit cost × qty.</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0" id="scannedItemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Barcodes</th>
                                        <th>Qty</th>
                                        <th>
                                            <span class="d-block">Unit cost (৳)</span>
                                            <small class="text-muted fw-normal">per barcode = 1 pc</small>
                                        </th>
                                        <th>
                                            <span class="d-block">Unit sell (৳)</span>
                                            <small class="text-muted fw-normal">optional</small>
                                        </th>
                                        <th>Line total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="scannedItemsBody"></tbody>
                                <tfoot class="table-light">
                                    <tr><th colspan="5" class="text-end">Items subtotal</th><th id="itemsSubtotal">৳0.00</th><th></th></tr>
                                </tfoot>
                            </table>
                        </div>
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
                                <label for="tax_amount" class="form-label small fw-semibold">Tax (৳)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm @error('tax_amount') is-invalid @enderror" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', 0) }}" oninput="calculateTotals()">
                                @error('tax_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="discount_amount" class="form-label small fw-semibold">Discount (৳)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm @error('discount_amount') is-invalid @enderror" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', 0) }}" oninput="calculateTotals()">
                                @error('discount_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="paid_amount" class="form-label small fw-semibold">Paid (৳)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm @error('paid_amount') is-invalid @enderror" id="paid_amount" name="paid_amount" value="{{ old('paid_amount', 0) }}" oninput="calculateTotals()">
                                @error('paid_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="p-3 rounded-3 mb-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Total</span><strong id="totalDisplay">৳0.00</strong></div>
                                <div class="d-flex justify-content-between small"><span class="text-muted">Due</span><strong id="dueDisplay" class="text-danger">৳0.00</strong></div>
                            </div>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label small fw-semibold">Payment method <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required onchange="toggleBankAccount()">
                                    <option value="cash" {{ old('payment_method', 'cash')=='cash'?'selected':'' }}>Cash</option>
                                    <option value="card" {{ old('payment_method')=='card'?'selected':'' }}>Card</option>
                                    <option value="mobile_banking" {{ old('payment_method')=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                                    <option value="bank_transfer" {{ old('payment_method')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                                    <option value="cheque" {{ old('payment_method')=='cheque'?'selected':'' }}>Cheque</option>
                                    <option value="other" {{ old('payment_method')=='other'?'selected':'' }}>Other</option>
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
                                <textarea class="form-control form-control-sm @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2" placeholder="Order notes...">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-0">
                                <label for="internal_notes" class="form-label small fw-semibold">Internal notes</label>
                                <textarea class="form-control form-control-sm @error('internal_notes') is-invalid @enderror" id="internal_notes" name="internal_notes" rows="2" placeholder="Private...">{{ old('internal_notes') }}</textarea>
                                @error('internal_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="d-none d-lg-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="submitBtnSidebar" disabled><i class="fas fa-save me-2"></i>Create Purchase Order</button>
                        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@php
$productsJson = $products->map(function($p) {
    return [
        'id' => $p->id,
        'name' => $p->name,
        'sku' => $p->sku,
        'cost_price' => (float) $p->cost_price,
        'selling_price' => $p->selling_price ? (float) $p->selling_price : null
    ];
})->values();
$suppliersJson = $suppliers->map(function($s) {
    return [
        'id' => $s->id,
        'name' => $s->name,
        'company_name' => $s->company_name ?? '',
        'phone' => $s->phone ?? '',
        'email' => $s->email ?? ''
    ];
})->values();
@endphp

@push('scripts')
<script>
const products = @json($productsJson);
const suppliers = @json($suppliersJson);

// Group items by product: {product_id: {product_name, cost_price, selling_price, barcodes: [barcode1, barcode2, ...]}}
let productItems = {};

let selectedProduct = null;

// Supplier Search
function initSupplierSearch() {
    const searchInput = document.getElementById('supplierSearch');
    const dropdown = document.getElementById('supplierDropdown');
    const supplierIdInput = document.getElementById('supplier_id');
    
    if (!searchInput || !dropdown) return;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';
        
        if (document.getElementById('walkInSupplier').checked) {
            dropdown.style.display = 'none';
            return;
        }
        
        if (query.length < 1) {
            dropdown.style.display = 'none';
            return;
        }
        
        const filtered = suppliers.filter(s =>
            (s.name && s.name.toLowerCase().includes(query)) ||
            (s.company_name && s.company_name.toLowerCase().includes(query)) ||
            (s.phone && s.phone.includes(query)) ||
            (s.email && s.email.toLowerCase().includes(query))
        );
        
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="p-2 text-muted">No suppliers found. Use <strong>Walk-in supplier</strong> for one-off purchases.</div>';
        } else {
            filtered.slice(0, 12).forEach(supplier => {
                const item = document.createElement('div');
                item.className = 'p-2 border-bottom';
                item.style.cursor = 'pointer';
                item.innerHTML = `<strong>${supplier.name}</strong>${supplier.company_name ? ' <small class="text-muted">— ' + supplier.company_name + '</small>' : ''}${supplier.phone ? ' <small class="text-muted d-block">' + supplier.phone + '</small>' : ''}`;
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
    
    searchInput.addEventListener('focus', function() {
        if (this.value.trim() && !document.getElementById('walkInSupplier').checked) {
            this.dispatchEvent(new Event('input'));
        }
    });
    
    document.addEventListener('click', function(e) {
        if (searchInput && !searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}

function toggleWalkInSupplier() {
    const walkIn = document.getElementById('walkInSupplier').checked;
    const fields = document.getElementById('walkInSupplierFields');
    const supplierId = document.getElementById('supplier_id');
    const searchInput = document.getElementById('supplierSearch');
    fields.style.display = walkIn ? 'flex' : 'none';
    if (walkIn) {
        supplierId.value = '';
        searchInput.value = '';
    } else {
        document.getElementById('supplier_name').value = '';
        document.getElementById('supplier_phone').value = '';
        document.getElementById('supplier_address').value = '';
    }
}

// Product Search Functionality
function initProductSearch() {
    const searchInput = document.getElementById('productSearch');
    const dropdown = document.getElementById('productDropdown');
    const productIdInput = document.getElementById('currentProduct');
    const selectedProductDiv = document.getElementById('selectedProduct');
    const selectedProductName = document.getElementById('selectedProductName');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';
        
        if (query.length < 1) {
            dropdown.style.display = 'none';
            return;
        }
        
        const filtered = products.filter(p => 
            p.name.toLowerCase().includes(query) || 
            (p.sku && p.sku.toLowerCase().includes(query))
        );
        
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="p-2 text-muted">No products found</div>';
        } else {
            filtered.slice(0, 10).forEach(product => {
                const item = document.createElement('div');
                item.className = 'p-2 cursor-pointer border-bottom';
                item.style.cursor = 'pointer';
                item.innerHTML = `<strong>${product.name}</strong> <small class="text-muted">(${product.sku})</small>`;
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
        selectedProductDiv.style.display = 'block';
        document.getElementById('barcodeScanner').focus();
    }
    
    // Close dropdown when clicking outside
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

function processBarcode(barcode) {
    if (!selectedProduct) {
        alert('Please select a product first');
        document.getElementById('barcodeScanner').value = '';
        document.getElementById('productSearch').focus();
        return;
    }
    
    const productId = selectedProduct.id;
    
    // Initialize product item if not exists
    if (!productItems[productId]) {
        productItems[productId] = {
            product_id: productId,
            product_name: selectedProduct.name,
            cost_price: selectedProduct.cost_price || 0,
            selling_price: selectedProduct.selling_price || null,
            barcodes: []
        };
    }
    
    // Check if barcode already exists for this product
    if (productItems[productId].barcodes.includes(barcode)) {
        alert('This barcode is already added for this product');
        document.getElementById('barcodeScanner').value = '';
        document.getElementById('barcodeScanner').focus();
        return;
    }
    
    // Add barcode to product
    productItems[productId].barcodes.push(barcode);
    
    // Visual feedback
    const scanner = document.getElementById('barcodeScanner');
    scanner.style.backgroundColor = '#d4edda';
    setTimeout(() => {
        scanner.style.backgroundColor = '';
        scanner.value = '';
        scanner.focus();
    }, 300);
    
    updateScannedItemsTable();
    calculateTotals();
}

function removeBarcode(productId, barcode) {
    if (productItems[productId] && productItems[productId].barcodes.includes(barcode)) {
        productItems[productId].barcodes = productItems[productId].barcodes.filter(b => b !== barcode);
        if (productItems[productId].barcodes.length === 0) {
            delete productItems[productId];
        }
        updateScannedItemsTable();
        calculateTotals();
    }
}

function removeProduct(productId) {
    delete productItems[productId];
    updateScannedItemsTable();
    calculateTotals();
}

function updateCostPrice(productId, newPrice) {
    if (productItems[productId]) {
        productItems[productId].cost_price = parseFloat(newPrice) || 0;
        updateScannedItemsTable();
        calculateTotals();
    }
}

function updateSellingPrice(productId, newPrice) {
    if (productItems[productId]) {
        productItems[productId].selling_price = newPrice ? parseFloat(newPrice) : null;
        updateScannedItemsTable();
    }
}

function updateScannedItemsTable() {
    const tbody = document.getElementById('scannedItemsBody');
    const totalCount = document.getElementById('totalItemsCount');
    
    tbody.innerHTML = '';
    
    let itemIndex = 0;
    let subtotal = 0;
    let totalQty = 0;
    
    Object.keys(productItems).forEach(productId => {
        const item = productItems[productId];
        const qty = item.barcodes.length;
        totalQty += qty;
        const rowSubtotal = item.cost_price * qty;
        subtotal += rowSubtotal;
        
        const barcodesHtml = item.barcodes.map(barcode => 
            `<span class="badge bg-primary me-1 mb-1">${barcode} <button type="button" class="btn-close btn-close-white btn-sm ms-1" style="font-size: 0.6em;" onclick="removeBarcode('${productId}', '${barcode}')" title="Remove barcode"></button></span>`
        ).join('');
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${item.product_name}</strong></td>
            <td>
                <div class="d-flex flex-wrap gap-1">${barcodesHtml}</div>
                <small class="text-muted">${qty} barcode(s)</small>
            </td>
            <td><strong>${qty}</strong></td>
            <td>
                <input type="number" step="0.01" class="form-control form-control-sm" value="${item.cost_price}" 
                       onchange="updateCostPrice('${productId}', this.value)" style="width: 120px;">
            </td>
            <td>
                <input type="number" step="0.01" class="form-control form-control-sm" value="${item.selling_price || ''}" 
                       onchange="updateSellingPrice('${productId}', this.value)" placeholder="Optional" style="width: 120px;">
            </td>
            <td><strong>৳${rowSubtotal.toFixed(2)}</strong></td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeProduct('${productId}')" title="Remove product">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        
        // Add hidden inputs for form submission - one per barcode
        item.barcodes.forEach(barcode => {
            const hiddenRow = document.createElement('tr');
            hiddenRow.style.display = 'none';
            hiddenRow.innerHTML = `
                <input type="hidden" name="items[${itemIndex}][product_id]" value="${item.product_id}">
                <input type="hidden" name="items[${itemIndex}][barcode]" value="${barcode}">
                <input type="hidden" name="items[${itemIndex}][cost_price]" value="${item.cost_price}">
                <input type="hidden" name="items[${itemIndex}][selling_price]" value="${item.selling_price || ''}">
                <input type="hidden" name="items[${itemIndex}][quantity]" value="1">
            `;
            tbody.appendChild(hiddenRow);
            itemIndex++;
        });
    });
    
    totalCount.textContent = totalQty;
    document.getElementById('itemsSubtotal').textContent = '৳' + subtotal.toFixed(2);
    
    var hasItems = Object.keys(productItems).length > 0;
    document.getElementById('submitBtn').disabled = !hasItems;
    var sidebarBtn = document.getElementById('submitBtnSidebar');
    if (sidebarBtn) sidebarBtn.disabled = !hasItems;
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
    Object.values(productItems).forEach(item => {
        subtotal += item.cost_price * item.barcodes.length;
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
    initSupplierSearch();
    toggleWalkInSupplier();
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
