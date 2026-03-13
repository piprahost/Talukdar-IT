@extends('layouts.dashboard')

@section('title', 'Purchase Order Details')
@section('page-title', 'Purchase Order Details')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-shopping-bag me-2"></i>Purchase Order: {{ $purchase->po_number }}</h6>
                <div>
                    <a href="{{ route('purchases.print', $purchase) }}" class="btn btn-sm btn-secondary me-2" target="_blank">
                        <i class="fas fa-print me-2"></i>Print Invoice
                    </a>
                    @if($purchase->status === 'received')
                        <a href="{{ route('purchase-returns.create', ['purchase_id' => $purchase->id]) }}" class="btn btn-sm btn-danger me-2">
                            <i class="fas fa-undo me-2"></i>Return Items
                        </a>
                    @else
                        <a href="{{ route('purchases.receive', $purchase) }}" class="btn btn-sm btn-success me-2">
                            <i class="fas fa-check me-2"></i>Receive Items
                        </a>
                        <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                    @endif
                    @if($purchase->returns->count() > 0)
                        <a href="{{ route('purchase-returns.index', ['search' => $purchase->po_number]) }}" class="btn btn-sm btn-info me-2">
                            <i class="fas fa-list me-2"></i>View Returns ({{ $purchase->returns->count() }})
                        </a>
                    @endif
                    <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <!-- Purchase Order Info -->
            <div class="row p-3">
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Purchase Information</h6>
                    <table class="table table-borderless">
                        <tr><th>PO Number:</th><td><strong>{{ $purchase->po_number }}</strong></td></tr>
                        <tr><th>Supplier:</th><td>{{ $purchase->supplier->name }}@if($purchase->supplier->company_name) ({{ $purchase->supplier->company_name }})@endif</td></tr>
                        <tr><th>Order Date:</th><td>{{ $purchase->order_date->format('d M Y') }}</td></tr>
                        @if($purchase->expected_delivery_date)<tr><th>Expected Delivery:</th><td>{{ $purchase->expected_delivery_date->format('d M Y') }}</td></tr>@endif
                        @if($purchase->received_date)<tr><th>Received Date:</th><td>{{ $purchase->received_date->format('d M Y') }}</td></tr>@endif
                        <tr><th>Status:</th><td><span class="badge {{ $purchase->status === 'received' ? 'bg-success' : ($purchase->status === 'partial' ? 'bg-warning text-dark' : 'bg-primary') }}">{{ ucfirst($purchase->status) }}</span></td></tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Financial Summary</h6>
                    <table class="table table-borderless">
                        <tr><th>Subtotal:</th><td><strong>৳{{ number_format($purchase->subtotal, 2) }}</strong></td></tr>
                        <tr><th>Tax:</th><td>৳{{ number_format($purchase->tax_amount, 2) }}</td></tr>
                        <tr><th>Discount:</th><td>৳{{ number_format($purchase->discount_amount, 2) }}</td></tr>
                        <tr><th>Total Amount:</th><td><strong>৳{{ number_format($purchase->total_amount, 2) }}</strong></td></tr>
                        <tr><th>Paid Amount:</th><td>৳{{ number_format($purchase->paid_amount, 2) }}</td></tr>
                        <tr><th>Due Amount:</th><td><strong class="text-danger">৳{{ number_format($purchase->due_amount, 2) }}</strong></td></tr>
                        <tr><th>Payment Method:</th><td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $purchase->payment_method ?? 'cash')) }}</span></td></tr>
                        @if($purchase->bankAccount)
                        <tr><th>Bank Account:</th><td>{{ $purchase->bankAccount->account_name }} - {{ $purchase->bankAccount->bank_name }}</td></tr>
                        @endif
                        <tr><th>Payment Status:</th><td><span class="badge {{ $purchase->payment_status === 'paid' ? 'bg-success' : ($purchase->payment_status === 'partial' ? 'bg-warning text-dark' : 'bg-danger') }}">{{ ucfirst($purchase->payment_status) }}</span></td></tr>
                    </table>
                </div>
            </div>
            
            @if($purchase->notes || $purchase->internal_notes)
            <div class="p-3 border-top">
                @if($purchase->notes)<div class="mb-2"><strong>Notes:</strong><p>{{ $purchase->notes }}</p></div>@endif
                @if($purchase->internal_notes)<div><strong>Internal Notes:</strong><p>{{ $purchase->internal_notes }}</p></div>@endif
            </div>
            @endif
            
            <!-- Purchase Items -->
            <div class="p-3 border-top">
                <h6 class="mb-3">Purchase Items ({{ $purchase->items->count() }})</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Barcode</th>
                                <th>Serial Number</th>
                                <th>Cost Price</th>
                                <th>Selling Price</th>
                                <th>Status</th>
                                <th>Received Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchase->items as $item)
                                <tr>
                                    <td><strong>{{ $item->product->name }}</strong></td>
                                    <td><code>{{ $item->barcode }}</code></td>
                                    <td>{{ $item->serial_number ?? 'N/A' }}</td>
                                    <td>৳{{ number_format($item->cost_price, 2) }}</td>
                                    <td>{{ $item->selling_price ? '৳' . number_format($item->selling_price, 2) : 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $item->status === 'received' ? 'bg-success' : ($item->status === 'damaged' ? 'bg-danger' : ($item->status === 'returned' ? 'bg-secondary' : 'bg-warning text-dark')) }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $item->received_date ? $item->received_date->format('d M Y') : 'N/A' }}</td>
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
@endsection

