@extends('layouts.dashboard')

@section('title', 'Edit Purchase Order')
@section('page-title', 'Edit Purchase Order')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-shopping-bag me-2"></i>Edit Purchase Order: {{ $purchase->po_number }}</h6>
                <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm">
                @csrf
                @method('PUT')
                
                <!-- Purchase Order Information -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i>Purchase Order Information</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select" id="supplier_id" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}@if($supplier->company_name) - {{ $supplier->company_name }}@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="order_date" name="order_date" value="{{ old('order_date', $purchase->order_date->format('Y-m-d')) }}" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="expected_delivery_date" class="form-label">Expected Delivery Date</label>
                            <input type="date" class="form-control" id="expected_delivery_date" name="expected_delivery_date" value="{{ old('expected_delivery_date', $purchase->expected_delivery_date?->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Financial Summary -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-dollar-sign me-2"></i>Financial Summary</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="tax_amount" class="form-label">Tax Amount (BDT)</label>
                            <input type="number" step="0.01" class="form-control" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', $purchase->tax_amount) }}" oninput="calculateTotals()">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="discount_amount" class="form-label">Discount Amount (BDT)</label>
                            <input type="number" step="0.01" class="form-control" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $purchase->discount_amount) }}" oninput="calculateTotals()">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="paid_amount" class="form-label">Paid Amount (BDT)</label>
                            <input type="number" step="0.01" class="form-control" id="paid_amount" name="paid_amount" value="{{ old('paid_amount', $purchase->paid_amount) }}" oninput="calculateTotals()">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Total Amount (BDT)</label>
                            <div class="form-control bg-light">
                                <strong>৳{{ number_format($purchase->total_amount, 2) }}</strong>
                            </div>
                            <small class="text-muted">Calculated from items</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required onchange="toggleBankAccount()">
                                <option value="cash" {{ old('payment_method', $purchase->payment_method ?? 'cash')=='cash'?'selected':'' }}>Cash</option>
                                <option value="card" {{ old('payment_method', $purchase->payment_method ?? 'cash')=='card'?'selected':'' }}>Card</option>
                                <option value="mobile_banking" {{ old('payment_method', $purchase->payment_method ?? 'cash')=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                                <option value="bank_transfer" {{ old('payment_method', $purchase->payment_method ?? 'cash')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                                <option value="cheque" {{ old('payment_method', $purchase->payment_method ?? 'cash')=='cheque'?'selected':'' }}>Cheque</option>
                                <option value="other" {{ old('payment_method', $purchase->payment_method ?? 'cash')=='other'?'selected':'' }}>Other</option>
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
                                    <option value="{{ $bankAccount->id }}" {{ old('bank_account_id', $purchase->bank_account_id)==$bankAccount->id?'selected':'' }}>
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
                                <strong class="text-danger">৳{{ number_format($purchase->due_amount, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes', $purchase->notes) }}</textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="internal_notes" class="form-label">Internal Notes</label>
                            <textarea class="form-control" id="internal_notes" name="internal_notes" rows="2">{{ old('internal_notes', $purchase->internal_notes) }}</textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Purchase Items (Read-only in edit mode) -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-box me-2"></i>Purchase Items</h6>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        Items cannot be modified after purchase order is created. To add/remove items, create a new purchase order.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Barcode</th>
                                    <th>Serial Number</th>
                                    <th>Quantity</th>
                                    <th>Cost Price</th>
                                    <th>Selling Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchase->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td><code>{{ $item->barcode }}</code></td>
                                        <td>{{ $item->serial_number ?? 'N/A' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>৳{{ number_format($item->cost_price, 2) }}</td>
                                        <td>{{ $item->selling_price ? '৳' . number_format($item->selling_price, 2) : 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $item->status === 'received' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center">No items found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
    const taxAmount = parseFloat(document.getElementById('tax_amount')?.value || 0);
    const discountAmount = parseFloat(document.getElementById('discount_amount')?.value || 0);
    const subtotal = {{ $purchase->subtotal }};
    const total = subtotal + taxAmount - discountAmount;
    
    // Update display if needed
}

document.addEventListener('DOMContentLoaded', function() {
    toggleBankAccount();
});
</script>
@endpush
@endsection

