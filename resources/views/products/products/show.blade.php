@extends('layouts.dashboard')

@section('title', 'Product Details')
@section('page-title', 'Product Details')

@section('content')
@php
    $margin = $product->selling_price > 0
        ? round((($product->selling_price - $product->cost_price) / $product->selling_price) * 100, 1)
        : 0;
    $profit = $product->selling_price - $product->cost_price;
@endphp

{{-- Price & Stock Overview Cards --}}
<div class="module-stats mb-3">
    <div class="module-stat-card">
        <div class="msc-label">Cost Price</div>
        <div class="msc-value" style="font-size:18px;">৳{{ number_format($product->cost_price, 2) }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #16a34a;">
        <div class="msc-label">Selling Price</div>
        <div class="msc-value" style="font-size:18px;color:#16a34a;">৳{{ number_format($product->selling_price, 2) }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid {{ $margin >= 20 ? '#16a34a' : ($margin >= 10 ? '#f59e0b' : '#ef4444') }};">
        <div class="msc-label">Profit Margin</div>
        <div class="msc-value" style="font-size:18px;color:{{ $margin >= 20 ? '#16a34a' : ($margin >= 10 ? '#f59e0b' : '#ef4444') }};">{{ $margin }}%</div>
        <div class="msc-sub">৳{{ number_format($profit, 2) }} per unit</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid {{ $product->stock_quantity <= 0 ? '#ef4444' : ($product->stock_quantity <= ($product->reorder_level ?? 0) ? '#f97316' : '#16a34a') }};">
        <div class="msc-label">Current Stock</div>
        <div class="msc-value" style="color:{{ $product->stock_quantity <= 0 ? '#ef4444' : ($product->stock_quantity <= ($product->reorder_level ?? 0) ? '#f97316' : '#16a34a') }};">
            {{ $product->stock_quantity }} {{ $product->unit }}
        </div>
        <div class="msc-sub">
            @if($product->stock_quantity <= 0) Out of stock
            @elseif($product->stock_quantity <= ($product->min_stock ?? 0)) Critical low
            @elseif($product->stock_quantity <= ($product->reorder_level ?? 0)) Low — reorder needed
            @else In stock
            @endif
        </div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #3b82f6;">
        <div class="msc-label">Stock Value</div>
        <div class="msc-value" style="font-size:16px;color:#3b82f6;">৳{{ number_format($product->stock_quantity * $product->cost_price, 0) }}</div>
        <div class="msc-sub">At cost price</div>
    </div>
</div>

<div class="row g-3">

    {{-- Main Details Card --}}
    <div class="col-md-8">
        <div class="table-card mb-3">
            <div class="table-card-header">
                <div>
                    <h6 class="mb-0"><i class="fas fa-box me-2"></i>{{ $product->name }}</h6>
                    @if($product->is_featured)
                    <small style="color:#f59e0b;font-weight:600;">⭐ Featured Product</small>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    @can('edit products')
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    @endcan
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:10px;">Product Details</h6>
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr>
                                <td class="text-muted ps-0" style="width:40%;">SKU</td>
                                <td class="pe-0"><code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;">{{ $product->sku }}</code></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Category</td>
                                <td class="pe-0">{{ $product->category->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Brand</td>
                                <td class="pe-0">{{ $product->brand->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Model</td>
                                <td class="pe-0">{{ $product->productModel->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Unit</td>
                                <td class="pe-0">{{ $product->unit ?? 'pcs' }}</td>
                            </tr>
                            @if($product->warranty_period)
                            <tr>
                                <td class="text-muted ps-0">Warranty</td>
                                <td class="pe-0">{{ $product->warranty_period }} days</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted ps-0">Status</td>
                                <td class="pe-0">
                                    @if(!$product->is_active)
                                        <span class="badge bg-secondary">Inactive</span>
                                    @elseif($product->stock_quantity <= 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:10px;">Stock Levels</h6>
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr>
                                <td class="text-muted ps-0" style="width:50%;">Current Stock</td>
                                <td class="pe-0 fw-semibold">{{ $product->stock_quantity }} {{ $product->unit }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Reorder Level</td>
                                <td class="pe-0">{{ $product->reorder_level ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Min Stock</td>
                                <td class="pe-0">{{ $product->min_stock ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Max Stock</td>
                                <td class="pe-0">{{ $product->max_stock ?? '—' }}</td>
                            </tr>
                        </table>

                        {{-- Stock level bar --}}
                        @php
                            $maxBar = max($product->max_stock ?? 100, $product->stock_quantity, 10);
                            $pct = min(100, round(($product->stock_quantity / $maxBar) * 100));
                            $barColor = $product->stock_quantity <= 0 ? '#ef4444' : ($product->stock_quantity <= ($product->min_stock ?? 0) ? '#f97316' : '#16a34a');
                        @endphp
                        <div class="mt-3">
                            <div style="font-size:11px;color:#9ca3af;margin-bottom:4px;">Stock Level</div>
                            <div style="background:#f3f4f6;border-radius:6px;height:8px;overflow:hidden;">
                                <div style="background:{{ $barColor }};height:100%;width:{{ $pct }}%;border-radius:6px;transition:width .4s;"></div>
                            </div>
                            <div style="font-size:11px;color:#9ca3af;margin-top:2px;">{{ $pct }}% of max ({{ $product->max_stock ?? '∞' }})</div>
                        </div>
                    </div>
                </div>

                @if($product->description || $product->specifications)
                <hr class="my-3">
                <div class="row">
                    @if($product->description)
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:6px;">Description</h6>
                        <p class="text-muted mb-0" style="font-size:13px;white-space:pre-line;">{{ $product->description }}</p>
                    </div>
                    @endif
                    @if($product->specifications)
                    <div class="col-md-6">
                        <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:6px;">Specifications</h6>
                        <p class="text-muted mb-0" style="font-size:13px;white-space:pre-line;">{{ $product->specifications }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Barcodes --}}
        @if($product->barcodes && count($product->barcodes) > 0)
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-barcode me-2"></i>Product Barcodes
                    <span class="badge bg-primary ms-1">{{ count($product->barcodes) }}</span>
                </h6>
                <small class="text-muted">Each barcode = 1 stock unit</small>
            </div>
            <div class="p-4">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($product->barcodes as $barcode)
                    <span class="badge" style="background:#f0fdf4;color:#166534;padding:8px 14px;font-size:13px;border:1px solid #dcfce7;border-radius:8px;font-family:monospace;">
                        <i class="fas fa-barcode me-1"></i>{{ $barcode }}
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="col-md-4">

        {{-- Pricing Card --}}
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-tag me-2"></i>Pricing</h6>
            </div>
            <div class="p-3">
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:13px;color:#6b7280;">Cost Price</span>
                    <span style="font-size:14px;font-weight:700;">৳{{ number_format($product->cost_price, 2) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:13px;color:#6b7280;">Selling Price</span>
                    <span style="font-size:14px;font-weight:700;color:#16a34a;">৳{{ number_format($product->selling_price, 2) }}</span>
                </div>
                @if($product->discount_price)
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:13px;color:#6b7280;">Discount Price</span>
                    <span style="font-size:14px;font-weight:700;color:#f59e0b;">৳{{ number_format($product->discount_price, 2) }}</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;padding:12px;background:{{ $margin >= 20 ? '#f0fdf4' : ($margin >= 10 ? '#fefce8' : '#fef2f2') }};margin:0 -12px;border-radius:0 0 8px 8px;">
                    <span style="font-size:13px;font-weight:700;color:#374151;">Profit Margin</span>
                    <span style="font-size:16px;font-weight:800;color:{{ $margin >= 20 ? '#16a34a' : ($margin >= 10 ? '#d97706' : '#ef4444') }};">{{ $margin }}%</span>
                </div>
            </div>
        </div>

        {{-- Quick Stock Adjustment --}}
        @can('adjust stock')
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-warehouse me-2"></i>Adjust Stock</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('products.adjust-stock', $product) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Movement Type</label>
                        <select class="form-select" name="type" required>
                            <option value="in">📦 Stock In (add)</option>
                            <option value="out">📤 Stock Out (remove)</option>
                            <option value="adjustment">🔧 Manual Adjustment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="quantity" placeholder="Enter quantity" required min="1">
                        <div class="form-text">Current stock: <strong>{{ $product->stock_quantity }}</strong> {{ $product->unit }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <input type="text" class="form-control" name="notes" placeholder="Reason for adjustment...">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Apply Adjustment
                    </button>
                </form>
            </div>
        </div>
        @endcan

        {{-- Internal Notes --}}
        @if($product->notes)
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-sticky-note me-2"></i>Internal Notes</h6>
            </div>
            <div class="p-3">
                <p class="text-muted mb-0" style="font-size:13px;">{{ $product->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
