@extends('layouts.dashboard')

@section('title', 'Warranty Verification')
@section('page-title', 'Warranty Verification')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-shield-alt me-2"></i>Verify Warranty by Barcode</h6>
            </div>
            
            <div class="p-4">
                <form action="{{ route('warranties.verify-by-barcode') }}" method="POST" id="warrantyVerifyForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="barcode" class="form-label">
                            <strong>Scan or Enter Barcode</strong>
                            <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="focusBarcodeInput()" title="Focus for barcode scanning">
                                <i class="fas fa-barcode"></i> Focus Scanner
                            </button>
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg @error('barcode') is-invalid @enderror" 
                               id="barcode" 
                               name="barcode" 
                               placeholder="Scan or type barcode here..." 
                               autofocus 
                               required
                               autocomplete="off">
                        @error('barcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Position cursor here and scan barcode, or manually enter the barcode to verify warranty status.
                        </small>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Verify Warranty
                        </button>
                        <a href="{{ route('warranties.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>View All Warranties
                        </a>
                    </div>
                </form>
                
                @if(session('error'))
                    <div class="alert alert-danger mt-3">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Quick Info -->
        <div class="table-card mt-4">
            <div class="table-card-header">
                <h6><i class="fas fa-info-circle me-2"></i>About Warranty Verification</h6>
            </div>
            <div class="p-3">
                <ul class="mb-0">
                    <li>Warranties are automatically created when products are sold.</li>
                    <li>Warranty period is set in days per product.</li>
                    <li>Warranty starts from the date of sale completion.</li>
                    <li>You can verify warranty status anytime by scanning the product barcode.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function focusBarcodeInput() {
    const input = document.getElementById('barcode');
    input.focus();
    input.select();
    input.style.borderColor = '#0d6efd';
    setTimeout(() => {
        input.style.borderColor = '';
    }, 1000);
}

// Auto-submit on Enter key (for barcode scanners)
document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcode');
    let lastKeyTime = Date.now();
    
    barcodeInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && this.value.trim().length > 0) {
            e.preventDefault();
            document.getElementById('warrantyVerifyForm').submit();
        }
    });
    
    // Ctrl+B to focus scanner
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            focusBarcodeInput();
        }
    });
});
</script>
@endpush
@endsection

