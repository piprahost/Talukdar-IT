@extends('layouts.dashboard')

@section('title', 'Create Purchase Order')
@section('page-title', 'Create Purchase Order')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-shopping-bag me-2"></i>Create Purchase Order</h6>
                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
                @csrf
                
                <!-- Purchase Order Information -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i>Purchase Order Information</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}@if($supplier->company_name) - {{ $supplier->company_name }}@endif
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('order_date') is-invalid @enderror" id="order_date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required>
                            @error('order_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="expected_delivery_date" class="form-label">Expected Delivery Date</label>
                            <input type="date" class="form-control @error('expected_delivery_date') is-invalid @enderror" id="expected_delivery_date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}">
                            @error('expected_delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                <label class="form-label">Select Product <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="productSearch" placeholder="Search product by name or SKU..." autocomplete="off">
                                    <div class="position-absolute w-100 bg-white border border-top-0 rounded-bottom shadow-lg" id="productDropdown" style="display: none; max-height: 300px; overflow-y: auto; z-index: 1000;">
                                        <!-- Product options will be populated here -->
                                    </div>
                                    <input type="hidden" id="currentProduct" value="">
                                    <div id="selectedProduct" class="mt-2" style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                            <span><strong id="selectedProductName"></strong></span>
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
                                <input type="text" class="form-control form-control-lg" id="barcodeScanner" placeholder="Scan barcode here - multiple barcodes for same product" autofocus>
                                <small class="text-muted"><i class="fas fa-info-circle"></i> Scan multiple barcodes for the selected product. Each barcode = 1 stock unit.</small>
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
                        <h6>Scanned Items (<span id="totalItemsCount">0</span> items)</h6>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm" id="scannedItemsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Barcodes</th>
                                        <th>Qty</th>
                                        <th>Cost Price (BDT)</th>
                                        <th>Selling Price (BDT)</th>
                                        <th>Subtotal</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="scannedItemsBody">
                                    <!-- Scanned items will appear here -->
                                </tbody>
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
                        <strong>Instructions:</strong> Search and select a product, then scan multiple barcodes for that product. Each barcode = 1 stock unit. You can add multiple barcodes to the same product item.
                    </div>
                </div>
                
                <hr>
                
                <!-- Financial Summary -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-dollar-sign me-2"></i>Financial Summary</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="tax_amount" class="form-label">Tax Amount (BDT)</label>
                            <input type="number" step="0.01" class="form-control @error('tax_amount') is-invalid @enderror" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', 0) }}" oninput="calculateTotals()">
                            @error('tax_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="discount_amount" class="form-label">Discount Amount (BDT)</label>
                            <input type="number" step="0.01" class="form-control @error('discount_amount') is-invalid @enderror" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', 0) }}" oninput="calculateTotals()">
                            @error('discount_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label for="paid_amount" class="form-label">Paid Amount (BDT)</label>
                            <input type="number" step="0.01" class="form-control @error('paid_amount') is-invalid @enderror" id="paid_amount" name="paid_amount" value="{{ old('paid_amount', 0) }}" oninput="calculateTotals()">
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                <option value="cash" {{ old('payment_method', 'cash')=='cash'?'selected':'' }}>Cash</option>
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
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="internal_notes" class="form-label">Internal Notes</label>
                            <textarea class="form-control @error('internal_notes') is-invalid @enderror" id="internal_notes" name="internal_notes" rows="2">{{ old('internal_notes') }}</textarea>
                            @error('internal_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="fas fa-save me-2"></i>Create Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>
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
@endphp

@push('scripts')
<script>
const products = @json($productsJson);

// Group items by product: {product_id: {product_name, cost_price, selling_price, barcodes: [barcode1, barcode2, ...]}}
let productItems = {};

let selectedProduct = null;

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
    
    // Enable/disable submit button
    document.getElementById('submitBtn').disabled = Object.keys(productItems).length === 0;
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
