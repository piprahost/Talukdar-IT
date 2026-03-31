@extends('layouts.dashboard')

@section('title', 'Create Service Return')
@section('page-title', 'Create Service Return')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-undo me-2"></i>Create Service Return</h6>
                <a href="{{ route('service-returns.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('service-returns.store') }}" method="POST">
                @csrf
                
                <div class="p-4">
                    <!-- Service Selection -->
                    <div class="mb-4">
                        <label for="service_id" class="form-label">Select Service Order <span class="text-danger">*</span></label>
                        <div class="alert alert-info mb-2">
                            <label for="barcodeScanner" class="form-label mb-1">
                                <i class="fas fa-barcode me-2"></i>Scan Serial Number or Service Number
                                <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="focusBarcodeScanner()" title="Focus for barcode scanning">
                                    <i class="fas fa-barcode"></i> Focus
                                </button>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="barcodeScanner" 
                                   placeholder="Scan serial number or service number to find service order..." 
                                   autocomplete="off">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Scan the serial number or service number. Press Enter after scanning.
                            </small>
                        </div>
                        <div class="position-relative">
                            <input type="text" class="form-control @error('service_id') is-invalid @enderror" 
                                   id="serviceSearch" 
                                   placeholder="Search by service number, customer name, serial number..." 
                                   autocomplete="off"
                                   value="{{ $service ? $service->service_number : '' }}">
                            <div class="position-absolute w-100 bg-white border border-top-0 rounded-bottom shadow-lg" 
                                 id="serviceDropdown" 
                                 style="display: none; max-height: 300px; overflow-y: auto; z-index: 1000;">
                            </div>
                            <input type="hidden" id="service_id" name="service_id" value="{{ $service ? $service->id : '' }}" required>
                            @error('service_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="selectedService" class="mt-2" style="display: {{ $service ? 'block' : 'none' }};">
                            @if($service)
                            <div class="alert alert-info">
                                <strong>Service #:</strong> {{ $service->service_number }} | 
                                <strong>Customer:</strong> {{ $service->customer_name }} | 
                                <strong>Product:</strong> {{ $service->product_name }} |
                                @if($service->serial_number)
                                    <strong>Serial #:</strong> {{ $service->serial_number }} |
                                @endif
                                <strong>Service Cost:</strong> ৳{{ number_format($service->service_cost, 2) }}
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Return Details -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Return Details</h6>
                        
                        <div class="mb-3">
                            <label for="return_date" class="form-label">Return Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('return_date') is-invalid @enderror" 
                                   id="return_date" name="return_date" 
                                   value="{{ old('return_date', date('Y-m-d')) }}" required>
                            @error('return_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="reason" class="form-label">Return Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                      id="reason" name="reason" rows="3" required 
                                      placeholder="Why is this service being returned?">{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="refund_amount" class="form-label">Refund Amount (BDT)</label>
                            <input type="number" step="0.01" class="form-control" id="refund_amount" name="refund_amount" 
                                   value="{{ old('refund_amount', $service ? $service->service_cost : 0) }}" min="0">
                            <small class="text-muted">Enter the amount to refund to customer</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" 
                                      placeholder="Additional notes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('service-returns.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary" {{ $service ? '' : 'disabled' }}>
                            <i class="fas fa-save me-2"></i>Create Return
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@php
// Build services array for JavaScript
$servicesArray = [];
if($service) {
    // If a specific service is selected, include it and all other services
    $servicesArray[] = [
        'id' => $service->id,
        'service_number' => $service->service_number,
        'serial_number' => $service->serial_number ?? '',
        'customer_name' => $service->customer_name,
        'product_name' => $service->product_name,
        'service_cost' => (float) $service->service_cost,
    ];
}

// Add all available services (excluding the already selected one if any)
foreach($services as $s) {
    if(!$service || $s->id != $service->id) {
        $servicesArray[] = [
            'id' => $s->id,
            'service_number' => $s->service_number,
            'serial_number' => $s->serial_number ?? '',
            'customer_name' => $s->customer_name,
            'product_name' => $s->product_name,
            'service_cost' => (float) $s->service_cost,
        ];
    }
}
@endphp

@push('scripts')
<script>
const services = @json(array_values($servicesArray));

function selectService(service) {
    if (!service || !service.id) {
        console.error('Invalid service object:', service);
        return;
    }
    
    const serviceIdInput = document.getElementById('service_id');
    const searchInput = document.getElementById('serviceSearch');
    const submitBtn = document.querySelector('button[type="submit"]');
    const dropdown = document.getElementById('serviceDropdown');
    const refundAmountInput = document.getElementById('refund_amount');
    const selectedServiceDiv = document.getElementById('selectedService');
    
    if (!serviceIdInput || !searchInput || !selectedServiceDiv) {
        console.error('Required elements not found');
        return;
    }
    
    serviceIdInput.value = service.id;
    searchInput.value = service.service_number || '';
    if (dropdown) dropdown.style.display = 'none';
    
    if (refundAmountInput) {
        refundAmountInput.value = service.service_cost || 0;
    }
    
    if (submitBtn) {
        submitBtn.disabled = false;
    }
    
    selectedServiceDiv.style.display = 'block';
    let html = `
        <strong>Service #:</strong> ${service.service_number || 'N/A'} | 
        <strong>Customer:</strong> ${service.customer_name || 'N/A'} | 
        <strong>Product:</strong> ${service.product_name || 'N/A'} |
    `;
    if (service.serial_number) {
        html += `<strong>Serial #:</strong> ${service.serial_number} | `;
    }
    html += `<strong>Service Cost:</strong> ৳${parseFloat(service.service_cost || 0).toFixed(2)}`;
    
    const alertDiv = selectedServiceDiv.querySelector('.alert');
    if (alertDiv) {
        alertDiv.innerHTML = html;
    } else {
        selectedServiceDiv.innerHTML = `<div class="alert alert-info">${html}</div>`;
    }
}

function initServiceSearch() {
    const searchInput = document.getElementById('serviceSearch');
    const dropdown = document.getElementById('serviceDropdown');
    
    if (!searchInput || !dropdown) {
        console.error('Service search elements not found');
        return;
    }
    
    // Check if services array is available
    if (!services || !Array.isArray(services)) {
        console.error('Services array not found or invalid:', services);
        return;
    }
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';
        
        if (query.length < 1) {
            dropdown.style.display = 'none';
            return;
        }
        
        const filtered = services.filter(s => {
            if (!s) return false;
            return (s.service_number && s.service_number.toLowerCase().includes(query)) || 
                   (s.customer_name && s.customer_name.toLowerCase().includes(query)) ||
                   (s.product_name && s.product_name.toLowerCase().includes(query)) ||
                   (s.serial_number && s.serial_number.toLowerCase().includes(query));
        });
        
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="p-2 text-muted">No services found</div>';
            dropdown.style.display = 'block';
        } else {
            filtered.slice(0, 10).forEach(service => {
                if (!service) return;
                
                const item = document.createElement('div');
                item.className = 'p-2 cursor-pointer border-bottom';
                item.style.cursor = 'pointer';
                item.innerHTML = `
                    <div><strong>${service.service_number || 'N/A'}</strong></div>
                    <div class="small text-muted">${service.customer_name || 'N/A'} - ${service.product_name || 'N/A'}</div>
                    ${service.serial_number ? `<div class="small text-info">Serial: ${service.serial_number}</div>` : ''}
                    <div class="small text-success">Cost: ৳${parseFloat(service.service_cost || 0).toFixed(2)}</div>
                `;
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    selectService(service);
                });
                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f0f0f0';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
                dropdown.appendChild(item);
            });
            dropdown.style.display = 'block';
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (searchInput && dropdown && 
            !searchInput.contains(e.target) && 
            !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
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
    
    if (!services || !Array.isArray(services) || services.length === 0) {
        alert('No services available for return.');
        return;
    }
    
    barcode = barcode.trim().toLowerCase();
    
    // Find service by serial number or service number
    const matchedService = services.find(s => {
        if (!s) return false;
        return (s.serial_number && s.serial_number.toLowerCase() === barcode) ||
               (s.service_number && s.service_number.toLowerCase() === barcode);
    });
    
    if (!matchedService) {
        alert('Service not found with this serial number or service number. Please check the barcode.');
        const scanner = document.getElementById('barcodeScanner');
        if (scanner) {
            scanner.value = '';
            scanner.focus();
        }
        return;
    }
    
    // Select the service
    selectService(matchedService);
    
    // Visual feedback
    const scanner = document.getElementById('barcodeScanner');
    if (scanner) {
        scanner.style.backgroundColor = '#d4edda';
        setTimeout(() => {
            scanner.style.backgroundColor = '';
            scanner.value = '';
            scanner.focus();
        }, 300);
    }
}

// Barcode scanning support
let lastKeyTime = Date.now();
let barcodeInput = '';

document.addEventListener('DOMContentLoaded', function() {
    initServiceSearch();
    
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
});
</script>
@endpush
@endsection

