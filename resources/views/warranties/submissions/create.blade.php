@extends('layouts.dashboard')

@section('title', 'Create Warranty Submission')
@section('page-title', 'Create Warranty Submission')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-file-alt me-2"></i>Create Warranty Submission</h6>
                <a href="{{ route('warranty-submissions.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('warranty-submissions.store') }}" method="POST" id="submissionForm">
                @csrf
                
                <!-- Barcode Search -->
                <div class="alert alert-info mb-4">
                    <h6 class="mb-3"><i class="fas fa-barcode me-2"></i>Find Warranty by Barcode</h6>
                    <div class="row align-items-end">
                        <div class="col-md-8 mb-2">
                            <label class="form-label">Scan or Enter Barcode</label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="barcodeSearch" 
                                   placeholder="Scan barcode to find warranty..." 
                                   value="{{ $warranty ? $warranty->barcode : '' }}"
                                   {{ $warranty ? 'readonly' : 'autofocus' }}>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="searchWarranty()">
                                <i class="fas fa-search me-2"></i>Search Warranty
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Warranty Found Display -->
                <div id="warrantyInfo" class="alert alert-success mb-4" style="display: {{ $warranty ? 'block' : 'none' }};">
                    <h6><i class="fas fa-check-circle me-2"></i>Warranty Found</h6>
                    <div id="warrantyDetails">
                        @if($warranty)
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Product:</strong> {{ $warranty->product->name }}</p>
                                    <p><strong>Invoice:</strong> {{ $warranty->sale->invoice_number }}</p>
                                    <p><strong>Warranty Period:</strong> {{ $warranty->end_date->format('Y-m-d') }} ({{ $warranty->isActive() ? $warranty->daysRemaining() . ' days remaining' : 'Expired' }})</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Customer:</strong> {{ $warranty->customer ? $warranty->customer->name : ($warranty->sale->customer_name ?? 'Walk-in Customer') }}</p>
                                    <p><strong>Phone:</strong> {{ $warranty->customer ? $warranty->customer->phone : ($warranty->sale->customer_phone ?? '') }}</p>
                                    <p><strong>Status:</strong> <span class="badge {{ $warranty->isActive() ? 'bg-success' : 'bg-danger' }}">{{ $warranty->isActive() ? 'Active' : 'Expired' }}</span></p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <input type="hidden" id="warranty_id" name="warranty_id" value="{{ $warranty ? $warranty->id : '' }}" required>
                </div>
                
                <div id="warrantyError" class="alert alert-danger mb-4" style="display: none;">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span id="errorMessage"></span>
                </div>
                
                <!-- Submission Details -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i>Submission Details</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="submission_date" class="form-label">Submission Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('submission_date') is-invalid @enderror" 
                                   id="submission_date" name="submission_date" 
                                   value="{{ old('submission_date', date('Y-m-d')) }}" required>
                            @error('submission_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="barcode" class="form-label">Barcode</label>
                            <input type="text" class="form-control" id="barcode" name="barcode" 
                                   value="{{ old('barcode', $warranty ? $warranty->barcode : '') }}" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="problem_description" class="form-label">Problem Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('problem_description') is-invalid @enderror" 
                                  id="problem_description" name="problem_description" rows="3" required>{{ old('problem_description') }}</textarea>
                        @error('problem_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="customer_complaint" class="form-label">Customer Complaint <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('customer_complaint') is-invalid @enderror" 
                                  id="customer_complaint" name="customer_complaint" rows="3" required>{{ old('customer_complaint') }}</textarea>
                        @error('customer_complaint')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="condition" class="form-label">Physical Condition <span class="text-danger">*</span></label>
                            <select class="form-select @error('condition') is-invalid @enderror" id="condition" name="condition" required>
                                <option value="">Select Condition</option>
                                <option value="excellent" {{ old('condition')=='excellent'?'selected':'' }}>Excellent</option>
                                <option value="good" {{ old('condition')=='good'?'selected':'' }}>Good</option>
                                <option value="fair" {{ old('condition')=='fair'?'selected':'' }}>Fair</option>
                                <option value="poor" {{ old('condition')=='poor'?'selected':'' }}>Poor</option>
                                <option value="damaged" {{ old('condition')=='damaged'?'selected':'' }}>Damaged</option>
                            </select>
                            @error('condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expected_completion_date" class="form-label">Expected Completion Date</label>
                            <input type="date" class="form-control" id="expected_completion_date" name="expected_completion_date" 
                                   value="{{ old('expected_completion_date') }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="physical_condition_notes" class="form-label">Physical Condition Notes</label>
                        <textarea class="form-control" id="physical_condition_notes" name="physical_condition_notes" rows="2">{{ old('physical_condition_notes') }}</textarea>
                    </div>
                </div>
                
                <!-- Customer Information -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-user me-2"></i>Customer Information</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                   id="customer_name" name="customer_name" 
                                   value="{{ old('customer_name', $warranty ? ($warranty->customer ? $warranty->customer->name : ($warranty->sale->customer_name ?? '')) : '') }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                   id="customer_phone" name="customer_phone" 
                                   value="{{ old('customer_phone', $warranty ? ($warranty->customer ? $warranty->customer->phone : ($warranty->sale->customer_phone ?? '')) : '') }}" required>
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="customer_address" class="form-label">Address</label>
                        <textarea class="form-control" id="customer_address" name="customer_address" rows="2">{{ old('customer_address', $warranty ? ($warranty->customer ? $warranty->customer->address : ($warranty->sale->customer_address ?? '')) : '') }}</textarea>
                    </div>
                </div>
                
                <!-- Internal Notes -->
                <div class="mb-4">
                    <label for="internal_notes" class="form-label">Internal Notes</label>
                    <textarea class="form-control" id="internal_notes" name="internal_notes" rows="2">{{ old('internal_notes') }}</textarea>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('warranty-submissions.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="submitBtn" {{ $warranty ? '' : 'disabled' }}>
                        <i class="fas fa-save me-2"></i>Create Submission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function searchWarranty() {
    const barcode = document.getElementById('barcodeSearch').value.trim();
    
    if (!barcode) {
        alert('Please enter a barcode');
        return;
    }
    
    const warrantyInfo = document.getElementById('warrantyInfo');
    const warrantyError = document.getElementById('warrantyError');
    const warrantyDetails = document.getElementById('warrantyDetails');
    const submitBtn = document.getElementById('submitBtn');
    
    // Hide previous results
    warrantyInfo.style.display = 'none';
    warrantyError.style.display = 'none';
    
    // Fetch warranty
    fetch(`{{ route('warranty-submissions.warranty-by-barcode') }}?barcode=${barcode}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                warrantyError.style.display = 'block';
                document.getElementById('errorMessage').textContent = data.error;
                submitBtn.disabled = true;
                return;
            }
            
            const w = data.warranty;
            
            // Display warranty info
            warrantyDetails.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Product:</strong> ${w.product_name}</p>
                        <p><strong>Invoice:</strong> ${w.invoice_number}</p>
                        <p><strong>Warranty Period:</strong> ${w.end_date} (${w.is_active ? w.days_remaining + ' days remaining' : 'Expired'})</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Customer:</strong> ${w.customer_name}</p>
                        <p><strong>Phone:</strong> ${w.customer_phone}</p>
                        <p><strong>Status:</strong> <span class="badge ${w.is_active ? 'bg-success' : 'bg-danger'}">${w.is_active ? 'Active' : 'Expired'}</span></p>
                    </div>
                </div>
            `;
            
            // Set hidden inputs
            document.getElementById('warranty_id').value = w.id;
            document.getElementById('barcode').value = w.barcode;
            document.getElementById('customer_name').value = w.customer_name;
            document.getElementById('customer_phone').value = w.customer_phone;
            document.getElementById('customer_address').value = w.customer_address || '';
            
            warrantyInfo.style.display = 'block';
            submitBtn.disabled = false;
        })
        .catch(error => {
            warrantyError.style.display = 'block';
            document.getElementById('errorMessage').textContent = 'Error fetching warranty information.';
            submitBtn.disabled = true;
        });
}

// Auto-search on Enter
document.addEventListener('DOMContentLoaded', function() {
    const barcodeSearch = document.getElementById('barcodeSearch');
    
    barcodeSearch.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && this.value.trim().length > 0) {
            e.preventDefault();
            searchWarranty();
        }
    });
});
</script>
@endpush
@endsection

