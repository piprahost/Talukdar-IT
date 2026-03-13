@extends('layouts.dashboard')

@section('title', 'Stock Management')
@section('page-title', 'Stock Management')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-warehouse me-2"></i>Stock Movements</h6>
        <div>
            <a href="{{ route('stock.create-manual') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-2"></i>Manual Stock Entry
            </a>
            <a href="{{ route('stock.low-stock') }}" class="btn btn-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert
            </a>
        </div>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-2"><select class="form-select" name="product_id" onchange="this.form.submit()"><option value="">All Products</option>@foreach($products as $p)<option value="{{ $p->id }}" {{ request('product_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>@endforeach</select></div>
            <div class="col-md-3 mb-2"><select class="form-select" name="type" onchange="this.form.submit()"><option value="">All Types</option><option value="in" {{ request('type')=='in'?'selected':'' }}>In</option><option value="out" {{ request('type')=='out'?'selected':'' }}>Out</option><option value="adjustment" {{ request('type')=='adjustment'?'selected':'' }}>Adjustment</option></select></div>
            <div class="col-md-2 mb-2"><input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()"></div>
            <div class="col-md-2 mb-2"><input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()"></div>
            <div class="col-md-1 mb-2"><a href="{{ route('stock.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-times"></i></a></div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Date</th><th>Product</th><th>Type</th><th>Quantity</th><th>Previous</th><th>Current</th><th>Created By</th><th>Notes</th></tr></thead>
            <tbody>
                @forelse($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $movement->product->name }}</td>
                        <td><span class="badge {{ $movement->quantity > 0 ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($movement->type) }}</span></td>
                        <td><strong class="{{ $movement->quantity > 0 ? 'text-success' : 'text-danger' }}">{{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}</strong></td>
                        <td>{{ $movement->previous_stock }}</td>
                        <td><strong>{{ $movement->current_stock }}</strong></td>
                        <td>{{ $movement->creator->name ?? 'N/A' }}</td>
                        <td>{{ $movement->notes ? substr($movement->notes, 0, 30) . '...' : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No stock movements found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($movements->hasPages())
        <div class="d-flex justify-content-center mt-4 mb-3">
            {{ $movements->links() }}
        </div>
    @endif
</div>
@endsection

