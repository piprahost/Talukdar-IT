@extends('layouts.dashboard')

@section('title', 'Create Purchase Return')
@section('page-title', 'Create Purchase Return')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-undo me-2"></i>Create Purchase Return</h6>
                <a href="{{ route('purchase-returns.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('purchase-returns.store') }}" method="POST" id="returnForm">
                @csrf
                
                <div class="p-4">
                    <!-- Purchase Selection -->
                    <div class="mb-4">
                        <label for="purchase_id" class="form-label">Select Purchase Order <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" class="form-control @error('purchase_id') is-invalid @enderror" 
                                   id="purchaseSearch" 
                                   placeholder="Search by PO number, supplier name..." 
                                   autocomplete="off"
                                   value="{{ $purchase ? $purchase->po_number : '' }}">
                            <div class="position-absolute w-100 bg-white border border-top-0 rounded-bottom shadow-lg" 
                                 id="purchaseDropdown" 
                                 style="display: none; max-height: 300px; overflow-y: auto; z-index: 1000;">
                            </div>
                            <input type="hidden" id="purchase_id" name="purchase_id" value="{{ $purchase ? $purchase->id : '' }}" required>
                            @error('purchase_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="selectedPurchase" class="mt-2" style="display: {{ $purchase ? 'block' : 'none' }};">
                            @if($purchase)
                            <div class="alert alert-info">
                                <strong>PO:</strong> {{ $purchase->po_number }} | 
                                <strong>Supplier:</strong> {{ $purchase->supplier->name }} | 
                                <strong>Date:</strong> {{ $purchase->order_date->format('d M Y') }}
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($purchase)
                    <!-- Barcode Scanner -->
                    <div class="mb-4">
                        <div class="alert alert-info">
                            <div class="row align-items-end">
                                <div class="col-md-10">
                                    <label for="barcodeScanner" class="form-label mb-1">
                                        <i class="fas fa-barcode me-2"></i>Scan Barcode to Add Item
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="focusBarcodeScanner()" title="Focus for barcode scanning">
                                            <i class="fas fa-barcode"></i> Focus
                                        </button>
                                    </label>
                                    <input type="text" class="form-control form-control-lg" id="barcodeScanner" 
                                           placeholder="Scan barcode to automatically select item for return..." 
                                           autocomplete="off">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> Scan the barcode of the item you want to return. Press Enter after scanning.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Return Items Selection -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Select Items to Return</h6>
                        <div class="table-responsive">
                            <table class="table table-hover" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Product</th>
                                        <th>Barcode</th>
                                        <th>Cost Price</th>
                                        <th>Qty to Return</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchase->items as $item)
                                        <tr data-item-id="{{ $item->id }}">
                                            <td>
                                                <input type="checkbox" class="item-checkbox" 
                                                       data-item-id="{{ $item->id }}"
                                                       data-product-id="{{ $item->product_id }}"
                                                       data-barcode="{{ $item->barcode }}"
                                                       data-cost-price="{{ $item->cost_price }}">
                                            </td>
                                            <td><strong>{{ $item->product->name }}</strong></td>
                                            <td><code>{{ $item->barcode }}</code></td>
                                            <td>৳{{ number_format($item->cost_price, 2) }}</td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm return-qty" 
                                                       min="1" max="1" value="1" 
                                                       data-item-id="{{ $item->id }}"
                                                       style="width: 80px;" disabled>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm return-reason" 
                                                       placeholder="Reason..." 
                                                       data-item-id="{{ $item->id }}"
                                                       disabled>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Selected Items Summary -->
                    <div class="mb-4" id="selectedItemsSummary" style="display: none;">
                        <h6>Selected Items for Return</h6>
                        <div class="table-responsive">
                            <table class="table table-sm" id="selectedItemsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Barcode</th>
                                        <th>Cost Price</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                        <th>Reason</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="selectedItemsBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Return Details -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Return Details</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="return_date" class="form-label">Return Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('return_date') is-invalid @enderror" 
                                       id="return_date" name="return_date" 
                                       value="{{ old('return_date', date('Y-m-d')) }}" required>
                                @error('return_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reason" class="form-label">Return Reason</label>
                            <textarea class="form-control" id="reason" name="reason" rows="2" 
                                      placeholder="General reason for return...">{{ old('reason') }}</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tax_amount" class="form-label">Tax Amount</label>
                                <input type="number" step="0.01" class="form-control" id="tax_amount" name="tax_amount" 
                                       value="{{ old('tax_amount', 0) }}" onchange="calculateTotal()">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="discount_amount" class="form-label">Discount Amount</label>
                                <input type="number" step="0.01" class="form-control" id="discount_amount" name="discount_amount" 
                                       value="{{ old('discount_amount', 0) }}" onchange="calculateTotal()">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>Total Return Amount:</strong> <span id="totalAmount">৳0.00</span>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('purchase-returns.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="fas fa-save me-2"></i>Create Return
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@php
if($purchase) {
    $purchasesJson = collect([$purchase])->map(function($p) {
        return [
            'id' => $p->id,
            'po_number' => $p->po_number,
            'supplier_name' => $p->supplier->name,
            'order_date' => $p->order_date->format('Y-m-d'),
        ];
    });
} else {
    $purchasesJson = $purchases->map(function($p) {
        return [
            'id' => $p->id,
            'po_number' => $p->po_number,
            'supplier_name' => $p->supplier->name,
            'order_date' => $p->order_date->format('Y-m-d'),
        ];
    });
}
@endphp

@push('scripts')
<script>
const purchases = @json($purchasesJson->values());
let selectedItems = {};

// Purchase Search
function initPurchaseSearch() {
    const searchInput = document.getElementById('purchaseSearch');
    const dropdown = document.getElementById('purchaseDropdown');
    const purchaseIdInput = document.getElementById('purchase_id');
    
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';
        
        if (query.length < 1) {
            dropdown.style.display = 'none';
            return;
        }
        
        const filtered = purchases.filter(p => 
            p.po_number.toLowerCase().includes(query) || 
            p.supplier_name.toLowerCase().includes(query)
        );
        
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="p-2 text-muted">No purchases found</div>';
        } else {
            filtered.slice(0, 10).forEach(purchase => {
                const item = document.createElement('div');
                item.className = 'p-2 cursor-pointer border-bottom';
                item.style.cursor = 'pointer';
                item.innerHTML = `
                    <div><strong>${purchase.po_number}</strong></div>
                    <div class="small text-muted">${purchase.supplier_name}</div>
                `;
                item.addEventListener('click', function() {
                    window.location.href = '{{ route("purchase-returns.create") }}?purchase_id=' + purchase.id;
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
    
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}


function addSelectedItem(checkbox) {
    const itemId = checkbox.dataset.itemId;
    const row = checkbox.closest('tr');
    const qtyInput = row.querySelector('.return-qty');
    const reasonInput = row.querySelector('.return-reason');
    
    selectedItems[itemId] = {
        purchase_item_id: itemId,
        product_id: checkbox.dataset.productId,
        barcode: checkbox.dataset.barcode,
        cost_price: parseFloat(checkbox.dataset.costPrice),
        quantity: parseInt(qtyInput.value),
        reason: reasonInput.value,
        product_name: row.querySelector('td:nth-child(2)').textContent.trim(),
    };
    
    qtyInput.addEventListener('input', function() {
        if (selectedItems[itemId]) {
            selectedItems[itemId].quantity = parseInt(this.value) || 1;
            updateSelectedItemsTable();
            calculateTotal();
        }
    });
    
    reasonInput.addEventListener('input', function() {
        if (selectedItems[itemId]) {
            selectedItems[itemId].reason = this.value;
        }
    });
}

function removeSelectedItem(itemId) {
    delete selectedItems[itemId];
    const checkbox = document.querySelector(`.item-checkbox[data-item-id="${itemId}"]`);
    if (checkbox) {
        checkbox.checked = false;
        const row = checkbox.closest('tr');
        row.querySelector('.return-qty').disabled = true;
        row.querySelector('.return-reason').disabled = true;
    }
}

function updateSelectedItemsTable() {
    const tbody = document.getElementById('selectedItemsBody');
    const summaryDiv = document.getElementById('selectedItemsSummary');
    
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (Object.keys(selectedItems).length === 0) {
        summaryDiv.style.display = 'none';
        document.getElementById('submitBtn').disabled = true;
        return;
    }
    
    summaryDiv.style.display = 'block';
    document.getElementById('submitBtn').disabled = false;
    
    Object.values(selectedItems).forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.product_name}</td>
            <td><code>${item.barcode}</code></td>
            <td>৳${item.cost_price.toFixed(2)}</td>
            <td>${item.quantity}</td>
            <td><strong>৳${(item.cost_price * item.quantity).toFixed(2)}</strong></td>
            <td>${item.reason || '-'}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSelectedItem('${item.purchase_item_id}'); updateSelectedItemsTable(); calculateTotal();">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        
        // Add hidden inputs
        const itemInputs = `
            <input type="hidden" name="items[${index}][purchase_item_id]" value="${item.purchase_item_id}">
            <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
            <input type="hidden" name="items[${index}][barcode]" value="${item.barcode}">
            <input type="hidden" name="items[${index}][cost_price]" value="${item.cost_price}">
            <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            <input type="hidden" name="items[${index}][reason]" value="${item.reason || ''}">
        `;
        row.insertAdjacentHTML('beforeend', itemInputs);
    });
}

function calculateTotal() {
    let subtotal = 0;
    Object.values(selectedItems).forEach(item => {
        subtotal += item.cost_price * item.quantity;
    });
    
    const taxAmount = parseFloat(document.getElementById('tax_amount')?.value || 0);
    const discountAmount = parseFloat(document.getElementById('discount_amount')?.value || 0);
    const total = subtotal + taxAmount - discountAmount;
    
    document.getElementById('totalAmount').textContent = '৳' + total.toFixed(2);
}

// Barcode Scanner Functions
function focusBarcodeScanner() {
    const scanner = document.getElementById('barcodeScanner');
    if (scanner) {
        scanner.focus();
        scanner.select();
        scanner.style.borderColor = '#10b981';
        setTimeout(() => scanner.style.borderColor = '', 1000);
    }
}

function processBarcodeScan(barcode) {
    if (!barcode || !barcode.trim()) {
        return;
    }
    
    barcode = barcode.trim();
    
    // Find item with matching barcode in the purchase items table
    const rows = document.querySelectorAll('#itemsTable tbody tr[data-item-id]');
    let foundItem = null;
    
    rows.forEach(row => {
        const codeElement = row.querySelector('code[data-barcode]');
        const rowBarcode = codeElement ? codeElement.dataset.barcode : null;
        if (rowBarcode === barcode) {
            foundItem = row;
        }
    });
    
    if (!foundItem) {
        alert('Barcode not found in this purchase order. Please check the barcode.');
        document.getElementById('barcodeScanner').value = '';
        document.getElementById('barcodeScanner').focus();
        return;
    }
    
    // Get the checkbox for this item
    const checkbox = foundItem.querySelector('.item-checkbox');
    const itemId = checkbox.dataset.itemId;
    
    // Check if already selected
    if (selectedItems[itemId]) {
        alert('This item is already selected for return.');
        document.getElementById('barcodeScanner').value = '';
        document.getElementById('barcodeScanner').focus();
        return;
    }
    
    // Check the checkbox and enable inputs
    checkbox.checked = true;
    const qtyInput = foundItem.querySelector('.return-qty');
    const reasonInput = foundItem.querySelector('.return-reason');
    qtyInput.disabled = false;
    reasonInput.disabled = false;
    
    // Add to selected items
    addSelectedItem(checkbox);
    updateSelectedItemsTable();
    calculateTotal();
    
    // Visual feedback
    const scanner = document.getElementById('barcodeScanner');
    scanner.style.backgroundColor = '#d4edda';
    setTimeout(() => {
        scanner.style.backgroundColor = '';
        scanner.value = '';
        scanner.focus();
    }, 300);
}

// Barcode scanning support
let lastKeyTime = Date.now();
let barcodeInput = '';

document.addEventListener('DOMContentLoaded', function() {
    initPurchaseSearch();
    
    const scanner = document.getElementById('barcodeScanner');
    
    if (scanner) {
        scanner.addEventListener('keydown', function(e) {
            const currentTime = Date.now();
            
            // Detect fast typing (barcode scanner) vs slow typing (manual entry)
            if (currentTime - lastKeyTime > 100) {
                barcodeInput = '';
            }
            lastKeyTime = currentTime;
            
            // Accumulate barcode input
            if (e.key.length === 1) {
                barcodeInput += e.key;
            }
            
            // Process on Enter key
            if (e.key === 'Enter' && this.value.trim().length > 0) {
                e.preventDefault();
                processBarcodeScan(this.value.trim());
            }
        });
        
        scanner.addEventListener('paste', function(e) {
            setTimeout(() => {
                if (this.value.trim()) {
                    processBarcodeScan(this.value.trim());
                }
            }, 10);
        });
        
        // Auto-focus on page load
        setTimeout(() => scanner.focus(), 500);
    }
    
    // Keyboard shortcut: Ctrl+B to focus scanner
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            focusBarcodeScanner();
        }
    });
    
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => {
                cb.checked = this.checked;
                toggleItemRow(cb);
            });
        });
    }
    
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleItemRow(this);
        });
    });
    
    function toggleItemRow(checkbox) {
        const row = checkbox.closest('tr');
        const qtyInput = row.querySelector('.return-qty');
        const reasonInput = row.querySelector('.return-reason');
        
        if (checkbox.checked) {
            qtyInput.disabled = false;
            reasonInput.disabled = false;
            addSelectedItem(checkbox);
        } else {
            qtyInput.disabled = true;
            reasonInput.disabled = true;
            removeSelectedItem(checkbox.dataset.itemId);
        }
        updateSelectedItemsTable();
        calculateTotal();
    }
});
</script>
@endpush
@endsection

