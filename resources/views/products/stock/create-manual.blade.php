@extends('layouts.dashboard')

@section('title', 'Manual Stock Entry')
@section('page-title', 'Manual Stock Entry')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-box me-2"></i>Manual Stock Entry</h6>
                <a href="{{ route('stock.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('stock.store-manual') }}" method="POST" id="manualStockForm">
                @csrf
                
                <!-- Entry Information -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i>Entry Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Entry Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <input type="text" class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" value="{{ old('notes') }}" placeholder="Optional notes for this entry">
                            @error('notes')
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
                                        <th>Quantity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="scannedItemsBody">
                                    <!-- Scanned items will appear here -->
                                </tbody>
                                <tfoot>
                                    <tr class="table-secondary">
                                        <th colspan="2" class="text-end">Total Items:</th>
                                        <th id="itemsTotal">0</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instructions:</strong> Search and select a product, then scan multiple barcodes for that product. Each barcode = 1 stock unit. You can add multiple barcodes to the same product item. Duplicate barcodes will be skipped.
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('stock.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="fas fa-save me-2"></i>Add Stock
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
        'sku' => $p->sku
    ];
})->values();
@endphp

@push('scripts')
<script>
const products = @json($productsJson);

// Group items by product: {product_id: {product_name, barcodes: [barcode1, barcode2, ...]}}
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
                item.className = 'p-2 border-bottom product-option';
                item.style.cursor = 'pointer';
                item.innerHTML = `
                    <div><strong>${product.name}</strong></div>
                    ${product.sku ? `<small class="text-muted">SKU: ${product.sku}</small>` : ''}
                `;
                item.addEventListener('click', function() {
                    selectProduct(product);
                    dropdown.style.display = 'none';
                    searchInput.value = '';
                });
                dropdown.appendChild(item);
            });
        }
        
        dropdown.style.display = filtered.length > 0 ? 'block' : 'none';
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}

function selectProduct(product) {
    selectedProduct = product;
    document.getElementById('currentProduct').value = product.id;
    document.getElementById('selectedProductName').textContent = product.name;
    document.getElementById('selectedProduct').style.display = 'block';
    document.getElementById('barcodeScanner').focus();
}

function clearProductSelection() {
    selectedProduct = null;
    document.getElementById('currentProduct').value = '';
    document.getElementById('selectedProduct').style.display = 'none';
    document.getElementById('productSearch').focus();
}

function focusBarcodeScanner() {
    const scanner = document.getElementById('barcodeScanner');
    scanner.focus();
    scanner.select();
    
    scanner.style.borderColor = '#10b981';
    setTimeout(function() {
        scanner.style.borderColor = '';
    }, 1000);
}

function addBarcodeManually() {
    if (!selectedProduct) {
        alert('Please select a product first');
        document.getElementById('productSearch').focus();
        return;
    }
    
    const barcodeInput = document.getElementById('barcodeScanner');
    const barcode = barcodeInput.value.trim();
    
    if (!barcode) {
        alert('Please enter a barcode');
        return;
    }
    
    addBarcodeToProduct(selectedProduct.id, barcode);
    barcodeInput.value = '';
    barcodeInput.focus();
}

function addBarcodeToProduct(productId, barcode) {
    if (!productItems[productId]) {
        const product = products.find(p => p.id == productId);
        productItems[productId] = {
            product_id: productId,
            product_name: product.name,
            barcodes: []
        };
    }
    
    // Check if barcode already exists in this product
    if (productItems[productId].barcodes.includes(barcode)) {
        alert(`Barcode ${barcode} already added for ${productItems[productId].product_name}`);
        return;
    }
    
    productItems[productId].barcodes.push(barcode);
    updateItemsTable();
    updateFormData();
}

function removeBarcode(productId, barcode) {
    if (productItems[productId]) {
        productItems[productId].barcodes = productItems[productId].barcodes.filter(b => b !== barcode);
        
        // Remove product from items if no barcodes left
        if (productItems[productId].barcodes.length === 0) {
            delete productItems[productId];
        }
        
        updateItemsTable();
        updateFormData();
    }
}

function updateItemsTable() {
    const tbody = document.getElementById('scannedItemsBody');
    tbody.innerHTML = '';
    
    let totalItems = 0;
    
    Object.values(productItems).forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${item.product_name}</strong></td>
            <td>
                <div class="d-flex flex-wrap gap-1">
                    ${item.barcodes.map(barcode => `
                        <span class="badge bg-primary">
                            ${barcode}
                            <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7em;" onclick="removeBarcode(${item.product_id}, '${barcode}')" title="Remove"></button>
                        </span>
                    `).join('')}
                </div>
            </td>
            <td><strong>${item.barcodes.length}</strong></td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeProduct(${item.product_id})">
                    <i class="fas fa-trash"></i> Remove All
                </button>
            </td>
        `;
        tbody.appendChild(row);
        totalItems += item.barcodes.length;
    });
    
    document.getElementById('totalItemsCount').textContent = Object.keys(productItems).length;
    document.getElementById('itemsTotal').textContent = totalItems;
    
    // Enable/disable submit button
    document.getElementById('submitBtn').disabled = Object.keys(productItems).length === 0;
}

function removeProduct(productId) {
    if (confirm('Remove all barcodes for this product?')) {
        delete productItems[productId];
        updateItemsTable();
        updateFormData();
    }
}

function updateFormData() {
    // Build items array for form submission
    const items = [];
    Object.values(productItems).forEach(item => {
        item.barcodes.forEach(barcode => {
            items.push({
                product_id: item.product_id,
                barcode: barcode
            });
        });
    });
    
    // Add hidden inputs for items
    const form = document.getElementById('manualStockForm');
    
    // Remove existing item inputs
    document.querySelectorAll('input[name^="items"]').forEach(input => {
        if (input.name.startsWith('items[')) {
            input.remove();
        }
    });
    
    // Add new item inputs
    items.forEach((item, index) => {
        Object.keys(item).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `items[${index}][${key}]`;
            input.value = item[key];
            form.appendChild(input);
        });
    });
}

// Barcode scanning support
let lastKeyTime = Date.now();

document.addEventListener('DOMContentLoaded', function() {
    initProductSearch();
    
    const scanner = document.getElementById('barcodeScanner');
    
    if (scanner) {
        scanner.addEventListener('keydown', function(e) {
            const currentTime = Date.now();
            if (currentTime - lastKeyTime > 100) {
                // Reset if typing manually
            }
            lastKeyTime = currentTime;
            
            // If Enter is pressed and we have input, treat it as barcode scan
            if (e.key === 'Enter' && this.value.length > 0) {
                e.preventDefault();
                
                // Visual feedback for successful scan
                this.style.backgroundColor = '#d4edda';
                setTimeout(function() {
                    scanner.style.backgroundColor = '';
                }, 300);
                
                addBarcodeManually();
            }
        });
        
        // Handle paste (some scanners use paste)
        scanner.addEventListener('paste', function(e) {
            setTimeout(function() {
                scanner.style.backgroundColor = '#d4edda';
                setTimeout(function() {
                    scanner.style.backgroundColor = '';
                }, 300);
                addBarcodeManually();
            }, 10);
        });
    }
    
    // Allow focusing barcode field with Ctrl+B or Cmd+B shortcut
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

