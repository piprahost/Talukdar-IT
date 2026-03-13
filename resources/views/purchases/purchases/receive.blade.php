@extends('layouts.dashboard')

@section('title', 'Receive Purchase Order')
@section('page-title', 'Receive Items')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-check-circle me-2"></i>Receive Items - PO: {{ $purchase->po_number }}</h6>
                <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <!-- Barcode Scanner Section -->
            <div class="alert alert-info mb-4">
                <h6><i class="fas fa-barcode me-2"></i>Scan Barcode to Receive Item</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                            <input type="text" class="form-control" id="barcodeScanner" placeholder="Scan barcode here..." autofocus>
                            <button type="button" class="btn btn-primary" onclick="scanBarcode()">Receive</button>
                        </div>
                        <small class="text-muted">Position cursor here and scan barcode, then press Enter or click Receive</small>
                    </div>
                </div>
            </div>
            
            <!-- Purchase Items -->
            <div class="mb-4">
                <h6 class="mb-3">Purchase Items</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>Product</th>
                                <th>Barcode</th>
                                <th>Serial Number</th>
                                <th>Cost Price</th>
                                <th>Condition</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchase->items as $item)
                                <tr data-barcode="{{ $item->barcode }}" class="{{ $item->status === 'received' ? 'table-success' : '' }}">
                                    <td>
                                        @if($item->status !== 'received')
                                            <input type="checkbox" class="item-checkbox" value="{{ $item->id }}">
                                        @endif
                                    </td>
                                    <td><strong>{{ $item->product->name }}</strong></td>
                                    <td><code>{{ $item->barcode }}</code></td>
                                    <td>{{ $item->serial_number ?? 'N/A' }}</td>
                                    <td>৳{{ number_format($item->cost_price, 2) }}</td>
                                    <td>{{ $item->condition_notes ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $item->status === 'received' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->status !== 'received')
                                            <form action="{{ route('purchases.receive-item', [$purchase, $item]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Receive
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-success"><i class="fas fa-check-circle"></i> Received</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center">No items found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($purchase->items->where('status', '!=', 'received')->count() > 0)
                <div class="mt-3">
                    <form action="{{ route('purchases.receive-multiple', $purchase) }}" method="POST" id="receiveMultipleForm">
                        @csrf
                        <button type="submit" class="btn btn-primary" id="receiveMultipleBtn" disabled>
                            <i class="fas fa-check-double me-2"></i>Receive Selected Items
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let receivedBarcodes = @json($purchase->items->where('status', 'received')->pluck('barcode')->toArray());

function scanBarcode() {
    const barcodeInput = document.getElementById('barcodeScanner');
    const barcode = barcodeInput.value.trim();
    
    if (!barcode) {
        alert('Please scan or enter a barcode');
        return;
    }
    
    // Find item with this barcode
    const itemRow = document.querySelector(`tr[data-barcode="${barcode}"]`);
    
    if (!itemRow) {
        alert('Barcode not found in this purchase order');
        barcodeInput.value = '';
        barcodeInput.focus();
        return;
    }
    
    const statusBadge = itemRow.querySelector('.badge');
    if (statusBadge && statusBadge.textContent.trim() === 'Received') {
        alert('This item is already received');
        barcodeInput.value = '';
        barcodeInput.focus();
        return;
    }
    
    // Find and click the receive button
    const receiveBtn = itemRow.querySelector('form button[type="submit"]');
    if (receiveBtn) {
        receiveBtn.click();
    } else {
        alert('Cannot receive this item');
    }
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateReceiveMultipleBtn();
}

function updateReceiveMultipleBtn() {
    const checked = document.querySelectorAll('.item-checkbox:checked');
    const btn = document.getElementById('receiveMultipleBtn');
    if (btn) {
        btn.disabled = checked.length === 0;
    }
    
    // Update hidden inputs
    const form = document.getElementById('receiveMultipleForm');
    if (form) {
        // Remove existing hidden inputs
        form.querySelectorAll('input[name="item_ids[]"]').forEach(inp => inp.remove());
        
        // Add new hidden inputs for selected items
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'item_ids[]';
            input.value = cb.value;
            form.appendChild(input);
        });
    }
}

// Barcode scanning support
let barcodeInput = '';
let lastKeyTime = Date.now();

document.addEventListener('DOMContentLoaded', function() {
    const scannerInput = document.getElementById('barcodeScanner');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateReceiveMultipleBtn);
    });
    
    if (scannerInput) {
        scannerInput.addEventListener('keydown', function(e) {
            const currentTime = Date.now();
            if (currentTime - lastKeyTime > 100) {
                barcodeInput = '';
            }
            lastKeyTime = currentTime;
            
            if (e.key === 'Enter' && this.value.length > 0) {
                e.preventDefault();
                scanBarcode();
                // Don't clear - let the form submission handle it
                setTimeout(() => {
                    this.value = '';
                    this.focus();
                }, 500);
            }
        });
        
        scannerInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                if (this.value.trim()) {
                    scanBarcode();
                    setTimeout(() => {
                        this.value = '';
                        this.focus();
                    }, 500);
                }
            }, 10);
        });
    }
    
    // Ctrl+B to focus scanner
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            if (scannerInput) {
                scannerInput.focus();
                scannerInput.select();
            }
        }
    });
});
</script>
@endpush
@endsection

