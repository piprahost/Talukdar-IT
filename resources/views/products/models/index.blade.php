@extends('layouts.dashboard')

@section('title', 'Product Models')
@section('page-title', 'Product Models')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-layer-group me-2"></i>Product Models</h6>
        @can('create product-models')
        <a href="{{ route('product-models.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Add Model
        </a>
        @endcan
    </div>

    <div class="filter-wrapper">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search models...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="brand_id" onchange="this.form.submit()">
                        <option value="">All Brands</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ request('brand_id')==$b->id?'selected':'' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
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
                    @if(request()->anyFilled(['search','brand_id','status']))
                    <a href="{{ route('product-models.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Model Name</th>
                    <th>Brand</th>
                    <th>Description</th>
                    <th class="text-center">Products</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($models as $model)
                <tr>
                    <td>
                        <strong style="font-size:14px;">{{ $model->name }}</strong>
                    </td>
                    <td>
                        <span class="badge" style="background:#eff6ff;color:#1d4ed8;font-size:12px;">{{ $model->brand->name ?? '—' }}</span>
                    </td>
                    <td style="font-size:13px;color:#6b7280;max-width:200px;">
                        {{ $model->description ? \Str::limit($model->description, 50) : '—' }}
                    </td>
                    <td class="text-center">
                        <a href="{{ route('products.index', ['brand_id'=>$model->brand_id]) }}" class="badge" style="background:#f3f4f6;color:#374151;font-weight:700;font-size:13px;text-decoration:none;">
                            {{ $model->products_count }}
                        </a>
                    </td>
                    <td class="text-center">
                        @if($model->is_active)
                            <span class="badge bg-success" style="font-size:11px;">Active</span>
                        @else
                            <span class="badge bg-secondary" style="font-size:11px;">Inactive</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('product-models.show', $model) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('product-models.edit', $model) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            @if($model->products_count == 0)
                            <form action="{{ route('product-models.destroy', $model) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete model \'{{ addslashes($model->name) }}\'?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="fas fa-layer-group fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">No product models found.</p>
                        @can('create product-models')
                        <a href="{{ route('product-models.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add First Model
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($models->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">{{ $models->total() }} models total</small>
        {{ $models->links() }}
    </div>
    @endif
</div>
@endsection
