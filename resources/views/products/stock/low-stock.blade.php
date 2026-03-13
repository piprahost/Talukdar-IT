@extends('layouts.dashboard')

@section('title', 'Low Stock Alert')
@section('page-title', 'Low Stock Alert')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Products</h6>
        <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary">Stock Management</a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Current Stock</th>
                    <th>Min Stock</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td><strong>{{ $product->name }}</strong></td>
                        <td><code>{{ $product->sku }}</code></td>
                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                        <td>{{ $product->brand->name ?? 'N/A' }}</td>
                        <td><span class="badge bg-danger">{{ $product->stock_quantity }} {{ $product->unit }}</span></td>
                        <td>{{ $product->min_stock }}</td>
                        <td>{{ $product->reorder_level }}</td>
                        <td><span class="badge bg-danger">Low Stock</span></td>
                        <td>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">View</a>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted">No low stock products. All products have sufficient stock!</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{ $products->links() }}
</div>
@endsection

