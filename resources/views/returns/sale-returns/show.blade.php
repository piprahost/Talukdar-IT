@extends('layouts.dashboard')

@section('title', 'Sale Return Details')
@section('page-title', 'Sale Return Details')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-undo me-2"></i>Return: {{ $saleReturn->return_number }}</h6>
                <div>
                    @if($saleReturn->status === 'pending')
                        <form action="{{ route('sale-returns.approve', $saleReturn) }}" method="POST" class="d-inline me-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this return?');">
                                <i class="fas fa-check me-2"></i>Approve
                            </button>
                        </form>
                        <a href="{{ route('sale-returns.edit', $saleReturn) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                    @endif
                    @if($saleReturn->status === 'approved')
                        <form action="{{ route('sale-returns.complete', $saleReturn) }}" method="POST" class="d-inline me-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Complete this return? Stock will be updated.');">
                                <i class="fas fa-check-double me-2"></i>Complete Return
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('sale-returns.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Return Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Return Number:</th><td><strong>{{ $saleReturn->return_number }}</strong></td></tr>
                            <tr><th>Invoice Number:</th><td><a href="{{ route('sales.show', $saleReturn->sale_id) }}">{{ $saleReturn->sale->invoice_number }}</a></td></tr>
                            <tr><th>Customer:</th><td>{{ $saleReturn->sale->customer_name ?? ($saleReturn->customer ? $saleReturn->customer->name : 'Walk-in') }}</td></tr>
                            <tr><th>Return Date:</th><td>{{ $saleReturn->return_date->format('d M Y') }}</td></tr>
                            <tr><th>Status:</th><td>
                                <span class="badge 
                                    {{ $saleReturn->status === 'completed' ? 'bg-success' : 
                                       ($saleReturn->status === 'cancelled' ? 'bg-danger' : 
                                       ($saleReturn->status === 'approved' ? 'bg-info' : 'bg-warning text-dark')) }}">
                                    {{ ucfirst($saleReturn->status) }}
                                </span>
                            </td></tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Financial Summary</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Subtotal:</th><td><strong>৳{{ number_format($saleReturn->subtotal, 2) }}</strong></td></tr>
                            <tr><th>Tax:</th><td>৳{{ number_format($saleReturn->tax_amount, 2) }}</td></tr>
                            <tr><th>Discount:</th><td>৳{{ number_format($saleReturn->discount_amount, 2) }}</td></tr>
                            <tr><th>Total Amount:</th><td><strong class="fs-5">৳{{ number_format($saleReturn->total_amount, 2) }}</strong></td></tr>
                        </table>
                    </div>
                </div>
                
                @if($saleReturn->reason || $saleReturn->notes)
                <div class="row mt-3">
                    <div class="col-md-12">
                        @if($saleReturn->reason)
                        <div class="mb-2">
                            <strong>Reason:</strong>
                            <p class="bg-light p-2 rounded">{{ $saleReturn->reason }}</p>
                        </div>
                        @endif
                        @if($saleReturn->notes)
                        <div>
                            <strong>Notes:</strong>
                            <p class="bg-light p-2 rounded">{{ $saleReturn->notes }}</p>
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
                                    <th>Unit Price</th>
                                    <th>Discount</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($saleReturn->items as $item)
                                    <tr>
                                        <td><strong>{{ $item->product->name }}</strong></td>
                                        <td><code>{{ $item->barcode ?? 'N/A' }}</code></td>
                                        <td>৳{{ number_format($item->unit_price, 2) }}</td>
                                        <td>৳{{ number_format($item->discount, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td><strong>৳{{ number_format($item->subtotal, 2) }}</strong></td>
                                        <td>{{ $item->reason ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center">No items found.</td></tr>
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

