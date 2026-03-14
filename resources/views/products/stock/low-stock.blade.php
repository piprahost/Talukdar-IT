@extends('layouts.dashboard')

@section('title', 'Low Stock Alert')
@section('page-title', 'Low Stock Alert')

@section('content')

@if($products->total() > 0)
{{-- Urgent alert banner --}}
<div class="d-flex align-items-center gap-3 p-4 rounded-3 mb-3" style="background:linear-gradient(135deg,#fef2f2,#fee2e2);border:1px solid #fecaca;border-left:5px solid #ef4444;">
    <div style="width:48px;height:48px;background:#ef4444;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fas fa-exclamation-triangle" style="color:white;font-size:22px;"></i>
    </div>
    <div>
        <div style="font-size:16px;font-weight:700;color:#991b1b;">⚠ {{ $products->total() }} Product{{ $products->total() > 1 ? 's' : '' }} Need Restocking</div>
        <div style="font-size:13px;color:#b91c1c;">These products have fallen below minimum stock levels. Take action to avoid stockouts.</div>
    </div>
    <div class="ms-auto d-flex gap-2">
        @can('create purchases')
        <a href="{{ route('purchases.create') }}" class="btn btn-danger">
            <i class="fas fa-shopping-cart me-1"></i>Order Stock
        </a>
        @endcan
    </div>
</div>
@endif

<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Low Stock Products</h6>
        <div class="d-flex gap-2">
            @can('view stock')
            <a href="{{ route('stock.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-history me-1"></i>Stock History
            </a>
            @endcan
            @can('create stock')
            <a href="{{ route('stock.create-manual') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i>Manual Stock Entry
            </a>
            @endcan
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category / Brand</th>
                    <th class="text-center">Current Stock</th>
                    <th class="text-center">Min Stock</th>
                    <th class="text-center">Reorder Level</th>
                    <th class="text-center">Shortage</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                @php
                    $shortage = max(0, ($product->reorder_level ?? 0) - $product->stock_quantity);
                    $isCritical = $product->stock_quantity <= ($product->min_stock ?? 0);
                    $isZero = $product->stock_quantity <= 0;
                @endphp
                <tr class="{{ $isZero ? 'table-danger' : ($isCritical ? 'table-warning' : '') }}" style="opacity:{{ $isZero ? '1' : '0.95' }};">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:8px;height:36px;border-radius:4px;background:{{ $isZero ? '#ef4444' : '#f97316' }};flex-shrink:0;"></div>
                            <div>
                                <div class="fw-semibold" style="font-size:14px;">{{ $product->name }}</div>
                                @if($isZero)
                                    <span style="font-size:10px;background:#ef4444;color:white;padding:1px 6px;border-radius:10px;font-weight:700;">OUT OF STOCK</span>
                                @elseif($isCritical)
                                    <span style="font-size:10px;background:#f97316;color:white;padding:1px 6px;border-radius:10px;font-weight:700;">CRITICAL</span>
                                @else
                                    <span style="font-size:10px;background:#eab308;color:white;padding:1px 6px;border-radius:10px;font-weight:700;">LOW</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td><code style="font-size:12px;background:#f3f4f6;padding:2px 6px;border-radius:4px;">{{ $product->sku }}</code></td>
                    <td style="font-size:13px;">
                        <div>{{ $product->category->name ?? '—' }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $product->brand->name ?? '' }}</div>
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:{{ $isZero ? '#ef4444' : '#f97316' }};color:white;font-size:14px;padding:6px 12px;font-weight:700;">
                            {{ $product->stock_quantity }} {{ $product->unit }}
                        </span>
                    </td>
                    <td class="text-center text-muted" style="font-size:13px;">{{ $product->min_stock ?? 0 }}</td>
                    <td class="text-center text-muted" style="font-size:13px;">{{ $product->reorder_level ?? 0 }}</td>
                    <td class="text-center">
                        @if($shortage > 0)
                            <span class="fw-bold text-danger">+{{ $shortage }} needed</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary" title="View Product"><i class="fas fa-eye"></i></a>
                            @can('adjust stock')
                            <a href="{{ route('products.show', $product) }}#adjustStockCard" class="btn btn-success" title="Adjust Stock"><i class="fas fa-plus"></i></a>
                            @endcan
                            @can('create purchases')
                            <a href="{{ route('purchases.create') }}" class="btn btn-outline-warning" title="Create Purchase Order"><i class="fas fa-shopping-cart"></i></a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3 d-block"></i>
                        <h5 class="text-success">All Good!</h5>
                        <p class="text-muted">All products have sufficient stock. No action needed.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-box me-1"></i>View All Products
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">{{ $products->total() }} products need attention</small>
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
