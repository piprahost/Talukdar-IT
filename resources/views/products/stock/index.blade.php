@extends('layouts.dashboard')

@section('title', 'Stock Management')
@section('page-title', 'Stock Management')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-warehouse me-2"></i>Stock Movements</h6>
        <div class="d-flex gap-2">
            @can('view stock')
            <a href="{{ route('stock.low-stock') }}" class="btn btn-sm btn-outline-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>Low Stock Alert
            </a>
            @endcan
            @can('create stock')
            <a href="{{ route('stock.create-manual') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i>Manual Entry
            </a>
            @endcan
        </div>
    </div>

    <div class="filter-wrapper">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <select class="form-select form-select-sm" name="product_id" onchange="this.form.submit()">
                        <option value="">All Products</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ request('product_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="type" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="in"         {{ request('type')=='in'         ?'selected':'' }}>Stock In</option>
                        <option value="out"        {{ request('type')=='out'        ?'selected':'' }}>Stock Out</option>
                        <option value="adjustment" {{ request('type')=='adjustment' ?'selected':'' }}>Adjustment</option>
                        <option value="return"     {{ request('type')=='return'     ?'selected':'' }}>Return</option>
                        <option value="sold"       {{ request('type')=='sold'       ?'selected':'' }}>Sold</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}" placeholder="From date" onchange="this.form.submit()">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}" placeholder="To date" onchange="this.form.submit()">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary flex-grow-1">Filter</button>
                    @if(request()->anyFilled(['product_id','type','date_from','date_to']))
                    <a href="{{ route('stock.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Product</th>
                    <th class="text-center">Type</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-center">Before</th>
                    <th class="text-center">After</th>
                    <th>Created By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                <tr>
                    <td style="font-size:12px;white-space:nowrap;">
                        <div>{{ $movement->created_at->format('d M Y') }}</div>
                        <div style="color:#9ca3af;">{{ $movement->created_at->format('h:i A') }}</div>
                    </td>
                    <td>
                        <strong style="font-size:14px;">{{ $movement->product->name ?? 'N/A' }}</strong>
                        @if($movement->product && $movement->product->sku)
                        <div style="font-size:11px;color:#9ca3af;">{{ $movement->product->sku }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        @php
                            $typeConfig = [
                                'in'         => ['bg'=>'#f0fdf4','color'=>'#166534','icon'=>'fa-arrow-down'],
                                'out'        => ['bg'=>'#fef2f2','color'=>'#991b1b','icon'=>'fa-arrow-up'],
                                'sold'       => ['bg'=>'#fef2f2','color'=>'#991b1b','icon'=>'fa-shopping-cart'],
                                'adjustment' => ['bg'=>'#eff6ff','color'=>'#1d4ed8','icon'=>'fa-sliders-h'],
                                'return'     => ['bg'=>'#f5f3ff','color'=>'#5b21b6','icon'=>'fa-undo'],
                                'damaged'    => ['bg'=>'#fff7ed','color'=>'#c2410c','icon'=>'fa-exclamation'],
                            ];
                            $tc = $typeConfig[$movement->type] ?? ['bg'=>'#f3f4f6','color'=>'#374151','icon'=>'fa-exchange-alt'];
                        @endphp
                        <span style="background:{{ $tc['bg'] }};color:{{ $tc['color'] }};padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;">
                            <i class="fas {{ $tc['icon'] }} me-1"></i>{{ ucfirst($movement->type) }}
                        </span>
                    </td>
                    <td class="text-center fw-bold" style="font-size:15px;color:{{ $movement->quantity > 0 ? '#16a34a' : '#ef4444' }};">
                        {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                    </td>
                    <td class="text-center" style="font-size:13px;color:#9ca3af;">{{ $movement->previous_stock }}</td>
                    <td class="text-center fw-semibold" style="font-size:13px;">{{ $movement->current_stock }}</td>
                    <td style="font-size:12px;">{{ $movement->creator->name ?? 'System' }}</td>
                    <td style="font-size:12px;max-width:180px;">
                        @if($movement->notes)
                            <span title="{{ $movement->notes }}">{{ Str::limit($movement->notes, 40) }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-boxes fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">No stock movements found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($movements->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">Showing {{ $movements->firstItem() }}–{{ $movements->lastItem() }} of {{ $movements->total() }} movements</small>
        {{ $movements->links() }}
    </div>
    @endif
</div>
@endsection
