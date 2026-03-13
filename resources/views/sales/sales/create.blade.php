@extends('layouts.dashboard')

@section('title', 'Create Sale / Invoice')
@section('page-title', 'Create Sale / Invoice')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-file-invoice-dollar me-2"></i>Create Sale / Invoice</h6>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
                @csrf
                
                <!-- Customer Information -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-user me-2"></i>Customer Information</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Select Customer</label>
                            <div class="position-relative">
                                <input type="text" class="form-control" id="customerSearch" placeholder="Search customer or select walk-in..." autocomplete="off">
                                <div class="position-absolute w-100 bg-white border border-top-0 rounded-bottom shadow-lg" id="customerDropdown" style="display: none; max-height: 300px; overflow-y: auto; z-index: 1000;"></div>
                                <input type="hidden" id="customer_id" name="customer_id" value="">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="walkInCustomer" onchange="toggleWalkIn()">
                                    <label class="form-check-label" for="walkInCustomer">Walk-in Customer</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3" id="walkInFields" style="display: none;">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Enter customer name">
                        </div>
                        <div class="col-md-4 mb-3" id="walkInPhone" style="display: none;">
                            <label for="customer_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="customer_phone" name="customer_phone" placeholder="Enter phone number">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sale_date" class="form-label">Sale Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}">
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Add Items by Scanning Barcodes -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-box me-2"></i>Add Items by Scanning Barcodes</h6>
                    
                    <!-- Product Selection and Barcode Scanner -->
                    <div class="alert alert-primary">
                        <div class="row align-items-end">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Search Product <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="productSearch" placeholder="Search product by name, SKU, or scan barcode..." autocomplete="off">
                                    <div class="position-absolute w-100 bg-white border border-top-0 rounded-bottom shadow-lg" id="productDropdown" style="display: none; max-height: 300px; overflow-y: auto; z-index: 1000;"></div>
                                    <input type="hidden" id="currentProduct" value="">
                                    <div id="selectedProduct" class="mt-2" style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                            <span><strong id="selectedProductName"></strong> <small class="text-muted" id="selectedProductStock"></small></span>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearProductSelection()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">
                                    Scan Barcode
                                    <button type="button" class="btn btn-sm btn-outline-light ms-2" onclick="focusBarcodeScanner()" title="Focus for barcode scanning">
                                        <i class="fas fa-barcode"></i> Scan
                                    </button>
                                </label>
                                <input type="text" class="form-control form-control-lg" id="barcodeScanner" placeholder="Scan barcode or enter manually" autofocus>
                                <small class="text-muted"><i class="fas fa-info-circle"></i> Scan barcode to add product. Each barcode = 1 unit.</small>
                            </div>
                            <div class="col-md-2 mb-2">
                                <button type="button" class="btn btn-primary w-100" onclick="addBarcodeManually()">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Scanned Items Table -->
                    <div class="mb-3">
                        <h6>Sale Items (<span id="totalItemsCount">0</span> items)</h6>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm" id="scannedItemsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Barcode</th>
                                        <th>Qty</th>
                                        <th>Unit Price (BDT)</th>
                                        <th>Discount (BDT)</th>
                                        <th>Subtotal</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="scannedItemsBody"></tbody>
                                <tfoot>
                                    <tr class="table-secondary">
                                        <th colspan="5" class="text-end">Subtotal:</th>
                                        <th id="itemsSubtotal">৳0.00</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instructions:</strong> Search and select a product, then scan barcode to add. Each barcode = 1 unit. You can add multiple items with different barcodes.
                    </div>
                </div>
                
                <hr>
                
                <!-- Financial Summary -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-dollar-sign me-2"></i>Financial Summary</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="tax_amount" class="form-label">Tax Amount (BDT)</label>
                            <input type="number" step="0.01" class="form-control" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', 0) }}" oninput="calculateTotals()">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="discount_amount" class="form-label">Discount Amount (BDT)</label>
                            <input type="number" step="0.01" class="form-control" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', 0) }}" oninput="calculateTotals()">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="paid_amount" class="form-label">Paid Amount (BDT)</label>
                            <input type="number" step="0.01" class="form-control" id="paid_amount" name="paid_amount" value="{{ old('paid_amount', 0) }}" oninput="calculateTotals()">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Total Amount (BDT)</label>
                            <div class="form-control bg-light">
                                <strong id="totalDisplay">৳0.00</strong>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required onchange="toggleBankAccount()">
                                <option value="cash" {{ old('payment_method')=='cash'?'selected':'' }}>Cash</option>
                                <option value="card" {{ old('payment_method')=='card'?'selected':'' }}>Card</option>
                                <option value="mobile_banking" {{ old('payment_method')=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                                <option value="bank_transfer" {{ old('payment_method')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                                <option value="cheque" {{ old('payment_method')=='cheque'?'selected':'' }}>Cheque</option>
                                <option value="other" {{ old('payment_method')=='other'?'selected':'' }}>Other</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3" id="bankAccountField" style="display: none;">
                            <label for="bank_account_id" class="form-label">Bank Account</label>
                            <select class="form-select @error('bank_account_id') is-invalid @enderror" id="bank_account_id" name="bank_account_id">
                                <option value="">Select Bank Account</option>
                                @foreach($bankAccounts as $bankAccount)
                                    <option value="{{ $bankAccount->id }}" {{ old('bank_account_id')==$bankAccount->id?'selected':'' }}>
                                        {{ $bankAccount->account_name }} - {{ $bankAccount->bank_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bank_account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Required for bank payments</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Due Amount</label>
                            <div class="form-control bg-light">
                                <strong id="dueDisplay" class="text-danger">৳0.00</strong>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="internal_notes" class="form-label">Internal Notes</label>
                            <textarea class="form-control" id="internal_notes" name="internal_notes" rows="2">{{ old('internal_notes') }}</textarea>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancel</a>
                    <div>
                        <button type="submit" name="status" value="draft" class="btn btn-warning me-2" id="saveDraftBtn">
                            <i class="fas fa-save me-2"></i>Save as Draft
                        </button>
                        <button type="submit" name="status" value="completed" class="btn btn-success" id="completeBtn" disabled>
                            <i class="fas fa-check me-2"></i>Complete Sale
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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

let productItems = {}; // {product_id: {product_name, barcode, unit_price, discount, quantity, stock}}
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
    document.getElementById('walkInFields').style.display = walkIn ? 'block' : 'none';
    document.getElementById('walkInPhone').style.display = walkIn ? 'block' : 'none';
    if (walkIn) {
        document.getElementById('customer_id').value = '';
        document.getElementById('customerSearch').value = '';
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

function processBarcode(barcode) {
    if (!selectedProduct) {
        // Try to find product by barcode
        fetch(`{{ route('sales.products-by-barcode') }}?barcode=${barcode}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    document.getElementById('barcodeScanner').value = '';
                    return;
                }
                // Select the product and add barcode
                const product = products.find(p => p.id === data.id);
                if (product) {
                    selectProduct(product);
                    addBarcodeToProduct(barcode, product);
                }
            })
            .catch(error => {
                alert('Product not found for this barcode');
                document.getElementById('barcodeScanner').value = '';
            });
        return;
    }
    
    addBarcodeToProduct(barcode, selectedProduct);
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

function addBarcodeToProduct(barcode, product) {
    const productId = product.id;
    
    // Check if barcode exists in product's barcodes
    if (product.barcodes && !product.barcodes.includes(barcode)) {
        alert('This barcode does not belong to the selected product');
        document.getElementById('barcodeScanner').value = '';
        return;
    }
    
    // Check stock
    if (product.stock_quantity <= 0) {
        alert('Product is out of stock');
        document.getElementById('barcodeScanner').value = '';
        return;
    }
    
    // Initialize product item if not exists
    if (!productItems[productId]) {
        productItems[productId] = {
            product_id: productId,
            product_name: product.name,
            barcodes: [],
            unit_price: product.selling_price || 0,
            discount: 0
        };
    }
    
    // Check if barcode already added
    if (productItems[productId].barcodes.includes(barcode)) {
        alert('This barcode is already added');
        document.getElementById('barcodeScanner').value = '';
        return;
    }
    
    // Add barcode
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

function updateUnitPrice(productId, newPrice) {
    if (productItems[productId]) {
        productItems[productId].unit_price = parseFloat(newPrice) || 0;
        updateScannedItemsTable();
        calculateTotals();
    }
}

function updateDiscount(productId, newDiscount) {
    if (productItems[productId]) {
        productItems[productId].discount = parseFloat(newDiscount) || 0;
        updateScannedItemsTable();
        calculateTotals();
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
        const itemSubtotal = (item.unit_price * qty) - item.discount;
        subtotal += itemSubtotal;
        
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
                <input type="number" step="0.01" class="form-control form-control-sm" value="${item.unit_price}" 
                       onchange="updateUnitPrice('${productId}', this.value)" style="width: 120px;">
            </td>
            <td>
                <input type="number" step="0.01" class="form-control form-control-sm" value="${item.discount}" 
                       onchange="updateDiscount('${productId}', this.value)" style="width: 100px;">
            </td>
            <td><strong>৳${itemSubtotal.toFixed(2)}</strong></td>
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
                <input type="hidden" name="items[${itemIndex}][unit_price]" value="${item.unit_price}">
                <input type="hidden" name="items[${itemIndex}][discount]" value="${item.discount}">
                <input type="hidden" name="items[${itemIndex}][quantity]" value="1">
            `;
            tbody.appendChild(hiddenRow);
            itemIndex++;
        });
    });
    
    totalCount.textContent = totalQty;
    document.getElementById('itemsSubtotal').textContent = '৳' + subtotal.toFixed(2);
    
    // Enable/disable buttons
    document.getElementById('completeBtn').disabled = Object.keys(productItems).length === 0;
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
        subtotal += (item.unit_price * item.barcodes.length) - item.discount;
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

