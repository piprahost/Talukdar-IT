@extends('layouts.dashboard')

@section('title', 'Edit Service')
@section('page-title', 'Edit Service Order')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-laptop-medical me-2"></i>Edit Service Order: #{{ $service->service_number }}</h6>
                <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
            
            <form action="{{ route('services.update', $service) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Product Information -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-box me-2"></i>Product Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('product_name') is-invalid @enderror" id="product_name" name="product_name" value="{{ old('product_name', $service->product_name) }}" placeholder="e.g., Laptop, Desktop, Printer" required>
                            @error('product_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="serial_number" class="form-label">
                                Serial Number / Barcode 
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="focusBarcodeField()" title="Focus for barcode scanning">
                                    <i class="fas fa-barcode"></i> Scan
                                </button>
                            </label>
                            <input type="text" class="form-control @error('serial_number') is-invalid @enderror" id="serial_number" name="serial_number" value="{{ old('serial_number', $service->serial_number) }}" placeholder="Scan barcode here or enter manually">
                            @error('serial_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Position cursor here and scan barcode
                            </small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="problem_notes" class="form-label">Problem Description</label>
                            <textarea class="form-control @error('problem_notes') is-invalid @enderror" id="problem_notes" name="problem_notes" rows="3" placeholder="Describe the problem reported by customer...">{{ old('problem_notes', $service->problem_notes) }}</textarea>
                            @error('problem_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="service_notes" class="form-label">Service Notes</label>
                            <textarea class="form-control @error('service_notes') is-invalid @enderror" id="service_notes" name="service_notes" rows="3" placeholder="Service technician notes...">{{ old('service_notes', $service->service_notes) }}</textarea>
                            @error('service_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Dates and Service Cost -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-calendar me-2"></i>Service Dates & Cost</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="receive_date" class="form-label">Receive Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('receive_date') is-invalid @enderror" id="receive_date" name="receive_date" value="{{ old('receive_date', $service->receive_date->format('Y-m-d')) }}" required>
                            @error('receive_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="delivery_date" class="form-label">Expected Delivery Date</label>
                            <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" id="delivery_date" name="delivery_date" value="{{ old('delivery_date', $service->delivery_date ? $service->delivery_date->format('Y-m-d') : '') }}">
                            @error('delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="service_cost" class="form-label">Service Cost (BDT) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('service_cost') is-invalid @enderror" id="service_cost" name="service_cost" value="{{ old('service_cost', $service->service_cost) }}" placeholder="0.00" required oninput="calculateDue()">
                            @error('service_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Amount in BDT (৳)</small>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Customer Information -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-user me-2"></i>Customer Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name', $service->customer_name) }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="customer_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $service->customer_phone) }}" required>
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="customer_address" class="form-label">Address</label>
                            <textarea class="form-control @error('customer_address') is-invalid @enderror" id="customer_address" name="customer_address" rows="2" placeholder="Customer address...">{{ old('customer_address', $service->customer_address) }}</textarea>
                            @error('customer_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Payment Information -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-money-bill-wave me-2"></i>Payment Information</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="paid_amount" class="form-label">Paid Amount (BDT) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('paid_amount') is-invalid @enderror" id="paid_amount" name="paid_amount" value="{{ old('paid_amount', $service->paid_amount) }}" placeholder="0.00" required oninput="calculateDue()">
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="due_amount" class="form-label">Due Amount (BDT)</label>
                            <input type="number" step="0.01" class="form-control @error('due_amount') is-invalid @enderror" id="due_amount" name="due_amount" value="{{ old('due_amount', $service->due_amount) }}" placeholder="0.00" readonly style="background-color: #f8f9fa;">
                            @error('due_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Calculated automatically</small>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required onchange="toggleBankAccount()">
                                <option value="cash" {{ old('payment_method', $service->payment_method ?? 'cash')=='cash'?'selected':'' }}>Cash</option>
                                <option value="card" {{ old('payment_method', $service->payment_method ?? 'cash')=='card'?'selected':'' }}>Card</option>
                                <option value="mobile_banking" {{ old('payment_method', $service->payment_method ?? 'cash')=='mobile_banking'?'selected':'' }}>Mobile Banking</option>
                                <option value="bank_transfer" {{ old('payment_method', $service->payment_method ?? 'cash')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                                <option value="cheque" {{ old('payment_method', $service->payment_method ?? 'cash')=='cheque'?'selected':'' }}>Cheque</option>
                                <option value="other" {{ old('payment_method', $service->payment_method ?? 'cash')=='other'?'selected':'' }}>Other</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3" id="bankAccountField" style="display: none;">
                            <label for="bank_account_id" class="form-label">Bank Account</label>
                            <select class="form-select @error('bank_account_id') is-invalid @enderror" id="bank_account_id" name="bank_account_id">
                                <option value="">Select Bank Account</option>
                                @foreach($bankAccounts as $bankAccount)
                                    <option value="{{ $bankAccount->id }}" {{ old('bank_account_id', $service->bank_account_id)==$bankAccount->id?'selected':'' }}>
                                        {{ $bankAccount->account_name }} - {{ $bankAccount->bank_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bank_account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Required for bank payments</small>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Payment Status</label>
                            <div class="mt-2">
                                <span id="payment_status" class="badge bg-secondary">Pending</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Status and Internal Notes -->
                <div class="mb-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="pending" {{ old('status', $service->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ old('status', $service->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $service->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="delivered" {{ old('status', $service->status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ old('status', $service->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="internal_notes" class="form-label">Internal Notes</label>
                            <textarea class="form-control @error('internal_notes') is-invalid @enderror" id="internal_notes" name="internal_notes" rows="2" placeholder="Internal notes (not visible to customer)...">{{ old('internal_notes', $service->internal_notes) }}</textarea>
                            @error('internal_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('services.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Service Order
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

function calculateDue() {
    const serviceCost = parseFloat(document.getElementById('service_cost').value) || 0;
    const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
    const dueAmount = Math.max(0, serviceCost - paidAmount);
    
    document.getElementById('due_amount').value = dueAmount.toFixed(2);
    
    // Update payment status
    const statusBadge = document.getElementById('payment_status');
    if (paidAmount === 0) {
        statusBadge.textContent = 'Unpaid';
        statusBadge.className = 'badge bg-danger';
    } else if (dueAmount === 0) {
        statusBadge.textContent = 'Paid';
        statusBadge.className = 'badge bg-success';
    } else {
        statusBadge.textContent = 'Partial';
        statusBadge.className = 'badge bg-warning';
    }
}

// Focus barcode field function
function focusBarcodeField() {
    const barcodeField = document.getElementById('serial_number');
    barcodeField.focus();
    barcodeField.select();
    
    // Visual feedback
    barcodeField.style.borderColor = '#10b981';
    setTimeout(function() {
        barcodeField.style.borderColor = '';
    }, 1000);
}

// Barcode scanning support
let barcodeInput = '';
let lastKeyTime = Date.now();

document.addEventListener('DOMContentLoaded', function() {
    calculateDue();
    toggleBankAccount();
    
    // Barcode field setup
    const barcodeField = document.getElementById('serial_number');
    if (barcodeField) {
        // Handle barcode scanner input (scanners typically send Enter after the barcode)
        barcodeField.addEventListener('keydown', function(e) {
            // Reset barcode input if too much time has passed (user typing manually)
            const currentTime = Date.now();
            if (currentTime - lastKeyTime > 100) {
                barcodeInput = '';
            }
            lastKeyTime = currentTime;
            
            // If Enter is pressed and we have input, treat it as barcode scan
            if (e.key === 'Enter' && this.value.length > 0) {
                e.preventDefault();
                
                // Visual feedback for successful scan
                this.style.backgroundColor = '#d4edda';
                setTimeout(function() {
                    barcodeField.style.backgroundColor = '';
                }, 300);
                
                // Move to next field
                document.getElementById('problem_notes').focus();
            }
        });
        
        // Handle paste (some scanners use paste)
        barcodeField.addEventListener('paste', function(e) {
            setTimeout(function() {
                barcodeField.style.backgroundColor = '#d4edda';
                setTimeout(function() {
                    barcodeField.style.backgroundColor = '';
                }, 300);
                document.getElementById('problem_notes').focus();
            }, 10);
        });
    }
    
    // Allow focusing barcode field with Ctrl+B or Cmd+B shortcut
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            focusBarcodeField();
        }
    });
});
</script>
@endpush
@endsection

