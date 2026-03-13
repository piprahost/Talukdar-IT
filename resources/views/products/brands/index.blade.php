@extends('layouts.dashboard')

@section('title', 'Brands')
@section('page-title', 'Product Brands')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-certificate me-2"></i>All Brands</h6>
        @can('create brands')
        <a href="{{ route('brands.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Add Brand
        </a>
        @endcan
    </div>

    <div class="filter-wrapper">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search brands...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active"   {{ request('status')=='active'   ?'selected':'' }}>Active</option>
                        <option value="inactive" {{ request('status')=='inactive' ?'selected':'' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary flex-grow-1">Search</button>
                    @if(request()->anyFilled(['search','status']))
                    <a href="{{ route('brands.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Brand Name</th>
                    <th>Description</th>
                    <th class="text-center">Products</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $brand)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                @if($brand->logo)
                                    <img src="{{ asset('storage/'.$brand->logo) }}" style="width:24px;height:24px;object-fit:contain;">
                                @else
                                    <i class="fas fa-certificate" style="color:#3b82f6;font-size:14px;"></i>
                                @endif
                            </div>
                            <div>
                                <strong style="font-size:14px;">{{ $brand->name }}</strong>
                                @if($brand->website)
                                <div style="font-size:11px;color:#9ca3af;">
                                    <a href="{{ $brand->website }}" target="_blank" style="color:#3b82f6;">{{ parse_url($brand->website, PHP_URL_HOST) }}</a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;color:#6b7280;max-width:250px;">
                        {{ $brand->description ? \Str::limit($brand->description, 60) : '—' }}
                    </td>
                    <td class="text-center">
                        <a href="{{ route('products.index', ['brand_id'=>$brand->id]) }}" class="badge" style="background:#eff6ff;color:#1d4ed8;font-weight:700;font-size:13px;text-decoration:none;">
                            {{ $brand->products_count }}
                        </a>
                    </td>
                    <td class="text-center">
                        @if($brand->is_active)
                            <span class="badge bg-success" style="font-size:11px;">Active</span>
                        @else
                            <span class="badge bg-secondary" style="font-size:11px;">Inactive</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('brands.show', $brand) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('brands.edit', $brand) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            @if($brand->products_count == 0)
                            <form action="{{ route('brands.destroy', $brand) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete brand \'{{ addslashes($brand->name) }}\'?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="fas fa-certificate fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">No brands found.</p>
                        @can('create brands')
                        <a href="{{ route('brands.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add First Brand
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($brands->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">{{ $brands->total() }} brands total</small>
        {{ $brands->links() }}
    </div>
    @endif
</div>
@endsection
