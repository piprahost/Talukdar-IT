@extends('layouts.dashboard')

@section('title', 'Products')
@section('page-title', 'Product Management')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-box me-2"></i>All Products</h6>
        @can('create products')
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Product
        </a>
        @endcan
    </div>
    
    <!-- Search and Filters -->
    <div class="mb-4">
        <form method="GET" action="{{ route('products.index') }}" id="filterForm">
            <div class="row filter-row mb-3">
                <div class="col-md-4 col-12 mb-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" id="search" value="{{ request('search') }}" placeholder="Search by name, SKU..." autofocus>
                        @if(request('search'))
                            <a href="{{ route('products.index', request()->except('search')) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                    <small class="text-muted">Search by SKU or product name</small>
                </div>
                
                <div class="col-md-2 col-6 mb-2">
                    <select class="form-select" name="category_id" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2 col-6 mb-2">
                    <select class="form-select" name="brand_id" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2 col-6 mb-2">
                    <select class="form-select" name="status" onchange="document.getElementById('filterForm').submit();">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    </select>
                </div>
                
                <div class="col-md-2 col-6 mb-2">
                    @if(request()->anyFilled(['search', 'category_id', 'brand_id', 'status']))
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-filter-circle-xmark me-2"></i>Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
    
    <!-- Products Table -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-box text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            @if($product->is_featured)
                                <span class="badge bg-warning text-dark ms-1">Featured</span>
                            @endif
                        </td>
                        <td><code>{{ $product->sku }}</code></td>
                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                        <td>{{ $product->brand->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $product->stock_quantity <= $product->min_stock ? 'bg-danger' : ($product->stock_quantity <= $product->reorder_level ? 'bg-warning text-dark' : 'bg-success') }}">
                                {{ $product->stock_quantity }} {{ $product->unit }}
                            </span>
                            @if($product->stock_quantity <= $product->min_stock)
                                <small class="d-block text-danger">Low Stock!</small>
                            @endif
                        </td>
                        <td>
                            <strong>৳{{ number_format($product->selling_price, 2) }}</strong>
                            @if($product->discount_price)
                                <br><small class="text-muted"><s>৳{{ number_format($product->cost_price, 2) }}</s></small>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $product->status === 'available' ? 'bg-success' : ($product->status === 'out_of_stock' ? 'bg-danger' : 'bg-secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $product->status)) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No products found. @can('create products')<a href="{{ route('products.create') }}">Create your first product</a>@else You don't have permission to create products.@endcan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($products->hasPages())
        <div class="d-flex justify-content-center mt-4 mb-3">
            {{ $products->links() }}
        </div>
    @endif
</div>

@endsection

