@extends('layouts.dashboard')

@section('title', 'Products')
@section('page-title', 'Product Management')

@section('content')

{{-- Stats --}}
<div class="module-stats mb-3">
    <div class="module-stat-card">
        <div class="msc-label">Total Products</div>
        <div class="msc-value">{{ $stats['total'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #16a34a;">
        <div class="msc-label">Active</div>
        <div class="msc-value" style="color:#16a34a;">{{ $stats['active'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #f97316;">
        <div class="msc-label">Low Stock</div>
        <div class="msc-value" style="color:#f97316;">{{ $stats['low_stock'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #ef4444;">
        <div class="msc-label">Out of Stock</div>
        <div class="msc-value" style="color:#ef4444;">{{ $stats['out_of_stock'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #3b82f6;">
        <div class="msc-label">Stock Value</div>
        <div class="msc-value" style="font-size:16px;color:#3b82f6;">৳{{ number_format($stats['total_value'], 0) }}</div>
        <div class="msc-sub">At cost price</div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-box me-2"></i>All Products</h6>
        <div class="d-flex gap-2">
            @can('view stock')
            <a href="{{ route('stock.low-stock') }}" class="btn btn-sm btn-outline-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
            </a>
            @endcan
            @can('create products')
            <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i>Add Product
            </a>
            @endcan
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-wrapper">
        <form method="GET" action="{{ route('products.index') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search name, SKU, barcode...">
                        @if(request('search'))
                            <a href="{{ route('products.index', request()->except('search', 'page')) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="category_id" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="brand_id" onchange="this.form.submit()">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active"       {{ request('status')=='active'       ?'selected':'' }}>Active</option>
                        <option value="low_stock"    {{ request('status')=='low_stock'    ?'selected':'' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('status')=='out_of_stock' ?'selected':'' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary flex-grow-1">Search</button>
                    @if(request()->anyFilled(['search','category_id','brand_id','status']))
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category / Brand</th>
                    <th class="text-center">Stock</th>
                    <th class="text-end">Cost</th>
                    <th class="text-end">Selling</th>
                    <th class="text-end">Margin</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                @php
                    $margin = $product->selling_price > 0
                        ? round((($product->selling_price - $product->cost_price) / $product->selling_price) * 100, 1)
                        : 0;
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:40px;height:40px;border-radius:8px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;">
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <i class="fas fa-box text-muted" style="font-size:16px;"></i>
                                @endif
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:14px;">{{ $product->name }}</div>
                                @if($product->productModel)
                                <div style="font-size:11px;color:#9ca3af;">{{ $product->productModel->name }}</div>
                                @endif
                                @if($product->is_featured)
                                    <span class="badge" style="background:#fef9c3;color:#854d0e;font-size:9px;">⭐ Featured</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td><code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:12px;">{{ $product->sku }}</code></td>
                    <td>
                        <div style="font-size:13px;">{{ $product->category->name ?? '—' }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $product->brand->name ?? '' }}</div>
                    </td>
                    <td class="text-center">
                        @if($product->stock_quantity <= 0)
                            <span class="badge bg-danger">0 {{ $product->unit }}</span>
                        @elseif($product->stock_quantity <= ($product->min_stock ?? 0))
                            <span class="badge bg-warning text-dark">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                        @elseif($product->stock_quantity <= ($product->reorder_level ?? 0))
                            <span class="badge bg-warning text-dark">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                        @else
                            <span class="badge bg-success">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                        @endif
                        @if($product->stock_quantity > 0 && $product->stock_quantity <= ($product->min_stock ?? 0))
                            <div style="font-size:10px;color:#ef4444;">Low Stock!</div>
                        @endif
                    </td>
                    <td class="text-end" style="font-size:13px;">৳{{ number_format($product->cost_price, 2) }}</td>
                    <td class="text-end fw-semibold">৳{{ number_format($product->selling_price, 2) }}
                        @if($product->discount_price)
                        <div style="font-size:11px;color:#16a34a;">Disc: ৳{{ number_format($product->discount_price, 2) }}</div>
                        @endif
                    </td>
                    <td class="text-end">
                        <span style="font-size:12px;font-weight:700;color:{{ $margin >= 20 ? '#16a34a' : ($margin >= 10 ? '#f59e0b' : '#ef4444') }};">
                            {{ $margin }}%
                        </span>
                    </td>
                    <td class="text-center">
                        @if(!$product->is_active)
                            <span class="badge bg-secondary" style="font-size:11px;">Inactive</span>
                        @elseif($product->stock_quantity <= 0)
                            <span class="badge bg-danger" style="font-size:11px;">Out of Stock</span>
                        @elseif($product->stock_quantity <= ($product->reorder_level ?? 0))
                            <span class="badge bg-warning text-dark" style="font-size:11px;">Low Stock</span>
                        @else
                            <span class="badge bg-success" style="font-size:11px;">In Stock</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            @can('adjust stock')
                            <a href="{{ route('products.adjust-stock', $product) }}" class="btn btn-outline-info" title="Adjust Stock"><i class="fas fa-warehouse"></i></a>
                            @endcan
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">No products found.</p>
                        @can('create products')
                        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add First Product
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">
            Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }} products
        </small>
        {{ $products->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const search = document.querySelector('input[name="search"]');
    if (search) {
        let timer;
        search.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                if (this.value.length >= 2 || this.value.length === 0) {
                    document.getElementById('filterForm').submit();
                }
            }, 500);
        });
    }
});
</script>
@endpush
@endsection
