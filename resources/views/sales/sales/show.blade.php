@extends('layouts.dashboard')

@section('title', 'Sale / Invoice Details')
@section('page-title', 'Sale / Invoice Details')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-file-invoice-dollar me-2"></i>Invoice: {{ $sale->invoice_number }}</h6>
                <div>
                    @if($sale->status === 'completed')
                        <a href="{{ route('sale-returns.create', ['sale_id' => $sale->id]) }}" class="btn btn-sm btn-danger me-2">
                            <i class="fas fa-undo me-2"></i>Return Items
                        </a>
                    @else
                        <a href="{{ route('sales.edit', $sale) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                        <form action="{{ route('sales.complete', $sale) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success me-2" onclick="return confirm('Complete this sale? Stock will be updated.');">
                                <i class="fas fa-check me-2"></i>Complete Sale
                            </button>
                        </form>
                    @endif
                    @if($sale->returns && $sale->returns->count() > 0)
                        <a href="{{ route('sale-returns.index', ['search' => $sale->invoice_number]) }}" class="btn btn-sm btn-info me-2">
                            <i class="fas fa-list me-2"></i>View Returns ({{ $sale->returns->count() }})
                        </a>
                    @endif
                    <a href="{{ route('sales.print', $sale) }}" class="btn btn-sm btn-secondary me-2" target="_blank">
                        <i class="fas fa-print me-2"></i>Print Invoice
                    </a>
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <!-- Sale Information -->
            <div class="row p-3">
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Sale Information</h6>
                    <table class="table table-borderless">
                        <tr><th>Invoice Number:</th><td><strong>{{ $sale->invoice_number }}</strong></td></tr>
                        <tr><th>Customer:</th><td>{{ $sale->customer ? $sale->customer->name : ($sale->customer_name ?? 'Walk-in Customer') }}</td></tr>
                        @if($sale->customer_phone)<tr><th>Phone:</th><td>{{ $sale->customer_phone }}</td></tr>@endif
                        @if($sale->customer_address)<tr><th>Address:</th><td>{{ $sale->customer_address }}</td></tr>@endif
                        <tr><th>Sale Date:</th><td>{{ $sale->sale_date->format('d M Y') }}</td></tr>
                        @if($sale->due_date)<tr><th>Due Date:</th><td>{{ $sale->due_date->format('d M Y') }}</td></tr>@endif
                        <tr><th>Status:</th><td><span class="badge {{ $sale->status === 'completed' ? 'bg-success' : ($sale->status === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ ucfirst($sale->status) }}</span></td></tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Financial Summary</h6>
                    <table class="table table-borderless">
                        <tr><th>Subtotal:</th><td><strong>৳{{ number_format($sale->subtotal, 2) }}</strong></td></tr>
                        <tr><th>Tax:</th><td>৳{{ number_format($sale->tax_amount, 2) }}</td></tr>
                        <tr><th>Discount:</th><td>৳{{ number_format($sale->discount_amount, 2) }}</td></tr>
                        <tr><th>Total Amount:</th><td><strong>৳{{ number_format($sale->total_amount, 2) }}</strong></td></tr>
                        <tr><th>Paid Amount:</th><td>৳{{ number_format($sale->paid_amount, 2) }}</td></tr>
                        <tr><th>Due Amount:</th><td><strong class="text-danger">৳{{ number_format($sale->due_amount, 2) }}</strong></td></tr>
                        <tr><th>Payment Method:</th><td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $sale->payment_method ?? 'cash')) }}</span></td></tr>
                        @if($sale->bankAccount)
                        <tr><th>Bank Account:</th><td>{{ $sale->bankAccount->account_name }} - {{ $sale->bankAccount->bank_name }}</td></tr>
                        @endif
                        <tr><th>Payment Status:</th><td><span class="badge {{ $sale->payment_status === 'paid' ? 'bg-success' : ($sale->payment_status === 'partial' ? 'bg-warning text-dark' : 'bg-danger') }}">{{ ucfirst($sale->payment_status) }}</span></td></tr>
                    </table>
                </div>
            </div>
            
            @if($sale->notes || $sale->internal_notes)
            <div class="p-3 border-top">
                @if($sale->notes)<div class="mb-2"><strong>Notes:</strong><p>{{ $sale->notes }}</p></div>@endif
                @if($sale->internal_notes)<div><strong>Internal Notes:</strong><p>{{ $sale->internal_notes }}</p></div>@endif
            </div>
            @endif
            
            <!-- Sale Items -->
            <div class="p-3 border-top">
                <h6 class="mb-3">Sale Items ({{ $sale->items->count() }})</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Barcode</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Discount</th>
                                <th>Warranty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sale->items as $item)
                                <tr>
                                    <td><strong>{{ $item->product->name }}</strong></td>
                                    <td><code>{{ $item->barcode ?? 'N/A' }}</code></td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>৳{{ number_format($item->unit_price, 2) }}</td>
                                    <td>৳{{ number_format($item->discount, 2) }}</td>
                                    <td>
                                        @if($item->product->warranty_period && $item->product->warranty_period > 0)
                                            <span class="badge bg-info">{{ $item->product->warranty_period }} days</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td><strong>৳{{ number_format($item->subtotal, 2) }}</strong></td>
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

