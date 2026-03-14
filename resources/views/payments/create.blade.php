@extends('layouts.dashboard')

@section('title', 'Record Payment')
@section('page-title', 'Record ' . ucfirst($type) . ' Payment')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-money-bill-wave me-2"></i>Record {{ ucfirst($type) }} Payment</h6>
                <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="payment_type" value="{{ $type }}">
                
                <div class="p-4">
                    @if($type === 'customer')
                        <div class="mb-3">
                            <label for="sale_id" class="form-label">Search Invoice <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" class="form-control @error('sale_id') is-invalid @enderror" 
                                       id="invoiceSearch" 
                                       placeholder="Search by invoice number, customer name..." 
                                       autocomplete="off">
                                <div class="position-absolute w-100 bg-white border border-top-0 rounded-bottom shadow-lg" 
                                     id="invoiceDropdown" 
                                     style="display: none; max-height: 300px; overflow-y: auto; z-index: 1000;">
                                    <!-- Invoice options will be populated here -->
                                </div>
                                <input type="hidden" id="sale_id" name="sale_id" value="{{ old('sale_id') }}" required>
                                @error('sale_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div id="selectedInvoice" class="mt-2" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                    <div>
                                        <strong id="selectedInvoiceNumber"></strong>
                                        <br><small class="text-muted" id="selectedCustomerName"></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearInvoiceSelection()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="customerInfo" class="mt-2 text-muted"></div>
                        </div>
                    @else
                        <div class="mb-3">
                            <label for="purchase_id" class="form-label">Search Purchase Order <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" class="form-control @error('purchase_id') is-invalid @enderror" 
                                       id="purchaseSearch" 
                                       placeholder="Search by PO number, supplier name..." 
                                       autocomplete="off">
                                <div class="position-absolute w-100 bg-white border border-top-0 rounded-bottom shadow-lg" 
                                     id="purchaseDropdown" 
                                     style="display: none; max-height: 300px; overflow-y: auto; z-index: 1000;">
                                    <!-- Purchase options will be populated here -->
                                </div>
                                <input type="hidden" id="purchase_id" name="purchase_id" value="{{ old('purchase_id') }}" required>
                                @error('purchase_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div id="selectedPurchase" class="mt-2" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                    <div>
                                        <strong id="selectedPONumber"></strong>
                                        <br><small class="text-muted" id="selectedSupplierName"></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearPurchaseSelection()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="supplierInfo" class="mt-2 text-muted"></div>
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                               id="amount" name="amount" value="{{ old('amount') }}" required>
                        <small class="text-muted">Due Amount: <strong id="dueAmount">৳0.00</strong></small>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                               id="payment_date" name="payment_date" 
                               value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                            <option value="cash" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='cash'?'selected':'' }}>Cash</option>
                            <option value="card" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='card'?'selected':'' }}>Card</option>
                            <option value="mobile_banking" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                            <option value="bank_transfer" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                            <option value="cheque" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='cheque'?'selected':'' }}>Cheque</option>
                            <option value="other" {{ old('payment_method', $defaultPaymentMethod ?? 'cash')=='other'?'selected':'' }}>Other</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="reference_number" class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="reference_number" name="reference_number" 
                               value="{{ old('reference_number') }}" 
                               placeholder="Transaction ID, Cheque Number, etc.">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Record Payment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@php
if($type === 'customer') {
    $itemsJson = $sales->map(function($sale) {
        return [
            'id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
            'customer_name' => $sale->customer ? $sale->customer->name : $sale->customer_name,
            'due_amount' => (float) $sale->due_amount,
            'total_amount' => (float) $sale->total_amount,
            'sale_date' => $sale->sale_date->format('d M Y'),
        ];
    })->values();
} else {
    $itemsJson = $purchases->map(function($purchase) {
        return [
            'id' => $purchase->id,
            'po_number' => $purchase->po_number,
            'supplier_name' => $purchase->supplier->name,
            'due_amount' => (float) $purchase->due_amount,
            'total_amount' => (float) $purchase->total_amount,
            'order_date' => $purchase->order_date->format('d M Y'),
        ];
    })->values();
}
@endphp

@push('scripts')
<script>
@if($type === 'customer')
const invoices = @json($itemsJson);

function initInvoiceSearch() {
    const searchInput = document.getElementById('invoiceSearch');
    const dropdown = document.getElementById('invoiceDropdown');
    const invoiceIdInput = document.getElementById('sale_id');
    const selectedInvoiceDiv = document.getElementById('selectedInvoice');
    const selectedInvoiceNumber = document.getElementById('selectedInvoiceNumber');
    const selectedCustomerName = document.getElementById('selectedCustomerName');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';
        
        if (query.length < 1) {
            dropdown.style.display = 'none';
            return;
        }
        
        const filtered = invoices.filter(inv => 
            inv.invoice_number.toLowerCase().includes(query) || 
            inv.customer_name.toLowerCase().includes(query)
        );
        
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="p-2 text-muted">No invoices found</div>';
        } else {
            filtered.slice(0, 10).forEach(invoice => {
                const item = document.createElement('div');
                item.className = 'p-2 cursor-pointer border-bottom';
                item.style.cursor = 'pointer';
                item.innerHTML = `
                    <div><strong>${invoice.invoice_number}</strong></div>
                    <div class="small text-muted">${invoice.customer_name}</div>
                    <div class="small text-success">Due: ৳${parseFloat(invoice.due_amount).toFixed(2)}</div>
                `;
                item.addEventListener('click', function() {
                    selectInvoice(invoice);
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
    
    function selectInvoice(invoice) {
        invoiceIdInput.value = invoice.id;
        searchInput.value = '';
        dropdown.style.display = 'none';
        selectedInvoiceNumber.textContent = invoice.invoice_number;
        selectedCustomerName.textContent = `${invoice.customer_name} - Due: ৳${parseFloat(invoice.due_amount).toFixed(2)}`;
        selectedInvoiceDiv.style.display = 'block';
        updateDueAmount(invoice.due_amount, invoice.customer_name);
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
    
    // Initialize with old value if exists
    @if(old('sale_id'))
        const oldInvoice = invoices.find(inv => inv.id == {{ old('sale_id') }});
        if (oldInvoice) {
            selectInvoice(oldInvoice);
        }
    @endif
}

function clearInvoiceSelection() {
    document.getElementById('sale_id').value = '';
    document.getElementById('invoiceSearch').value = '';
    document.getElementById('selectedInvoice').style.display = 'none';
    document.getElementById('dueAmount').textContent = '৳0.00';
    document.getElementById('customerInfo').textContent = '';
    document.getElementById('invoiceSearch').focus();
}

function updateDueAmount(dueAmount, customerName) {
    document.getElementById('dueAmount').textContent = '৳' + parseFloat(dueAmount).toFixed(2);
    document.getElementById('amount').max = dueAmount;
    document.getElementById('customerInfo').textContent = customerName ? 'Customer: ' + customerName : '';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initInvoiceSearch();
});
@else
const purchases = @json($itemsJson);

function initPurchaseSearch() {
    const searchInput = document.getElementById('purchaseSearch');
    const dropdown = document.getElementById('purchaseDropdown');
    const purchaseIdInput = document.getElementById('purchase_id');
    const selectedPurchaseDiv = document.getElementById('selectedPurchase');
    const selectedPONumber = document.getElementById('selectedPONumber');
    const selectedSupplierName = document.getElementById('selectedSupplierName');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';
        
        if (query.length < 1) {
            dropdown.style.display = 'none';
            return;
        }
        
        const filtered = purchases.filter(po => 
            po.po_number.toLowerCase().includes(query) || 
            po.supplier_name.toLowerCase().includes(query)
        );
        
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="p-2 text-muted">No purchase orders found</div>';
        } else {
            filtered.slice(0, 10).forEach(purchase => {
                const item = document.createElement('div');
                item.className = 'p-2 cursor-pointer border-bottom';
                item.style.cursor = 'pointer';
                item.innerHTML = `
                    <div><strong>${purchase.po_number}</strong></div>
                    <div class="small text-muted">${purchase.supplier_name}</div>
                    <div class="small text-success">Due: ৳${parseFloat(purchase.due_amount).toFixed(2)}</div>
                `;
                item.addEventListener('click', function() {
                    selectPurchase(purchase);
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
    
    function selectPurchase(purchase) {
        purchaseIdInput.value = purchase.id;
        searchInput.value = '';
        dropdown.style.display = 'none';
        selectedPONumber.textContent = purchase.po_number;
        selectedSupplierName.textContent = `${purchase.supplier_name} - Due: ৳${parseFloat(purchase.due_amount).toFixed(2)}`;
        selectedPurchaseDiv.style.display = 'block';
        updateDueAmount(purchase.due_amount, purchase.supplier_name);
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
    
    // Initialize with old value if exists
    @if(old('purchase_id'))
        const oldPurchase = purchases.find(po => po.id == {{ old('purchase_id') }});
        if (oldPurchase) {
            selectPurchase(oldPurchase);
        }
    @endif
}

function clearPurchaseSelection() {
    document.getElementById('purchase_id').value = '';
    document.getElementById('purchaseSearch').value = '';
    document.getElementById('selectedPurchase').style.display = 'none';
    document.getElementById('dueAmount').textContent = '৳0.00';
    document.getElementById('supplierInfo').textContent = '';
    document.getElementById('purchaseSearch').focus();
}

function updateDueAmount(dueAmount, supplierName) {
    document.getElementById('dueAmount').textContent = '৳' + parseFloat(dueAmount).toFixed(2);
    document.getElementById('amount').max = dueAmount;
    document.getElementById('supplierInfo').textContent = supplierName ? 'Supplier: ' + supplierName : '';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initPurchaseSearch();
});
@endif
</script>
@endpush
@endsection
