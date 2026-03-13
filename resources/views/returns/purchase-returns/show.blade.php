@extends('layouts.dashboard')

@section('title', 'Purchase Return Details')
@section('page-title', 'Purchase Return Details')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-undo me-2"></i>Return: {{ $purchaseReturn->return_number }}</h6>
                <div>
                    @if($purchaseReturn->status === 'pending')
                        <form action="{{ route('purchase-returns.approve', $purchaseReturn) }}" method="POST" class="d-inline me-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this return?');">
                                <i class="fas fa-check me-2"></i>Approve
                            </button>
                        </form>
                        <a href="{{ route('purchase-returns.edit', $purchaseReturn) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                    @endif
                    @if($purchaseReturn->status === 'approved')
                        <form action="{{ route('purchase-returns.complete', $purchaseReturn) }}" method="POST" class="d-inline me-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Complete this return? Stock will be updated.');">
                                <i class="fas fa-check-double me-2"></i>Complete Return
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('purchase-returns.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Return Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Return Number:</th><td><strong>{{ $purchaseReturn->return_number }}</strong></td></tr>
                            <tr><th>PO Number:</th><td><a href="{{ route('purchases.show', $purchaseReturn->purchase_id) }}">{{ $purchaseReturn->purchase->po_number }}</a></td></tr>
                            <tr><th>Supplier:</th><td>{{ $purchaseReturn->supplier->name }}</td></tr>
                            <tr><th>Return Date:</th><td>{{ $purchaseReturn->return_date->format('d M Y') }}</td></tr>
                            <tr><th>Status:</th><td>
                                <span class="badge 
                                    {{ $purchaseReturn->status === 'completed' ? 'bg-success' : 
                                       ($purchaseReturn->status === 'cancelled' ? 'bg-danger' : 
                                       ($purchaseReturn->status === 'approved' ? 'bg-info' : 'bg-warning text-dark')) }}">
                                    {{ ucfirst($purchaseReturn->status) }}
                                </span>
                            </td></tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Financial Summary</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Subtotal:</th><td><strong>৳{{ number_format($purchaseReturn->subtotal, 2) }}</strong></td></tr>
                            <tr><th>Tax:</th><td>৳{{ number_format($purchaseReturn->tax_amount, 2) }}</td></tr>
                            <tr><th>Discount:</th><td>৳{{ number_format($purchaseReturn->discount_amount, 2) }}</td></tr>
                            <tr><th>Total Amount:</th><td><strong class="fs-5">৳{{ number_format($purchaseReturn->total_amount, 2) }}</strong></td></tr>
                        </table>
                    </div>
                </div>
                
                @if($purchaseReturn->reason || $purchaseReturn->notes)
                <div class="row mt-3">
                    <div class="col-md-12">
                        @if($purchaseReturn->reason)
                        <div class="mb-2">
                            <strong>Reason:</strong>
                            <p class="bg-light p-2 rounded">{{ $purchaseReturn->reason }}</p>
                        </div>
                        @endif
                        @if($purchaseReturn->notes)
                        <div>
                            <strong>Notes:</strong>
                            <p class="bg-light p-2 rounded">{{ $purchaseReturn->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Return Items -->
                <div class="mt-4">
                    <h6 class="border-bottom pb-2 mb-3">Return Items</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Barcode</th>
                                    <th>Cost Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseReturn->items as $item)
                                    <tr>
                                        <td><strong>{{ $item->product->name }}</strong></td>
                                        <td><code>{{ $item->barcode }}</code></td>
                                        <td>৳{{ number_format($item->cost_price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td><strong>৳{{ number_format($item->subtotal, 2) }}</strong></td>
                                        <td>{{ $item->reason ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center">No items found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

