@extends('layouts.dashboard')

@section('title', 'Product Details')
@section('page-title', 'Product Details')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-box me-2"></i>{{ $product->name }}</h6>
                <div>
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <div class="row p-3">
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Basic Information</h6>
                    <table class="table table-borderless">
                        <tr><th>Name:</th><td><strong>{{ $product->name }}</strong></td></tr>
                        <tr><th>SKU:</th><td><code>{{ $product->sku }}</code></td></tr>
                        <tr><th>Category:</th><td>{{ $product->category->name ?? 'N/A' }}</td></tr>
                        <tr><th>Brand:</th><td>{{ $product->brand->name ?? 'N/A' }}</td></tr>
                        <tr><th>Model:</th><td>{{ $product->productModel->name ?? 'N/A' }}</td></tr>
                        <tr><th>Status:</th><td><span class="badge {{ $product->status === 'available' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst(str_replace('_', ' ', $product->status)) }}</span></td></tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Pricing & Stock</h6>
                    <table class="table table-borderless">
                        <tr><th>Cost Price:</th><td><strong>৳{{ number_format($product->cost_price, 2) }}</strong></td></tr>
                        <tr><th>Selling Price:</th><td><strong>৳{{ number_format($product->selling_price, 2) }}</strong></td></tr>
                        @if($product->discount_price)<tr><th>Discount Price:</th><td><strong>৳{{ number_format($product->discount_price, 2) }}</strong></td></tr>@endif
                        <tr><th>Stock Quantity:</th><td><span class="badge {{ $product->stock_quantity <= $product->min_stock ? 'bg-danger' : ($product->stock_quantity <= $product->reorder_level ? 'bg-warning text-dark' : 'bg-success') }}">{{ $product->stock_quantity }} {{ $product->unit }}</span></td></tr>
                        <tr><th>Reorder Level:</th><td>{{ $product->reorder_level }}</td></tr>
                        <tr><th>Min Stock:</th><td>{{ $product->min_stock }}</td></tr>
                        @if($product->max_stock)<tr><th>Max Stock:</th><td>{{ $product->max_stock }}</td></tr>@endif
                    </table>
                </div>
            </div>
            
            @if($product->description || $product->specifications)
            <div class="p-3 border-top">
                @if($product->description)<div class="mb-3"><strong>Description:</strong><p>{{ $product->description }}</p></div>@endif
                @if($product->specifications)<div><strong>Specifications:</strong><p>{{ $product->specifications }}</p></div>@endif
            </div>
            @endif
            
            @if($product->barcodes && count($product->barcodes) > 0)
            <div class="p-3 border-top">
                <h6 class="mb-3"><i class="fas fa-barcode me-2"></i>Product Barcodes ({{ count($product->barcodes) }})</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($product->barcodes as $barcode)
                        <span class="badge bg-primary fs-6 px-3 py-2">
                            <i class="fas fa-barcode me-1"></i>{{ $barcode }}
                        </span>
                    @endforeach
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="fas fa-info-circle"></i> Each barcode represents 1 stock unit. Barcodes are added when items are received from purchase orders.
                </small>
            </div>
            @endif
            
            <!-- Stock Adjustment Form -->
            <div class="p-3 border-top">
                <h6 class="mb-3">Adjust Stock</h6>
                <form action="{{ route('products.adjust-stock', $product) }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-3">
                        <select class="form-select" name="type" required>
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control" name="quantity" placeholder="Quantity" required min="1">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="notes" placeholder="Notes (optional)">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

