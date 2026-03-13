@extends('layouts.dashboard')

@section('title', 'Categories')
@section('page-title', 'Product Categories')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-tags me-2"></i>All Categories</h6>
        @can('create categories')
        <a href="{{ route('categories.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Add Category
        </a>
        @endcan
    </div>

    <div class="filter-wrapper">
        <form method="GET" action="{{ route('categories.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search categories...">
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
                    <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th class="text-center">Products</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-tag" style="color:#16a34a;font-size:14px;"></i>
                            </div>
                            <strong style="font-size:14px;">{{ $category->name }}</strong>
                        </div>
                    </td>
                    <td style="font-size:13px;color:#6b7280;max-width:250px;">
                        {{ $category->description ? \Str::limit($category->description, 60) : '—' }}
                    </td>
                    <td class="text-center">
                        <a href="{{ route('products.index', ['category_id'=>$category->id]) }}" class="badge" style="background:#eff6ff;color:#1d4ed8;font-weight:700;font-size:13px;text-decoration:none;">
                            {{ $category->products_count }}
                        </a>
                    </td>
                    <td class="text-center">
                        @if($category->is_active)
                            <span class="badge bg-success" style="font-size:11px;">Active</span>
                        @else
                            <span class="badge bg-secondary" style="font-size:11px;">Inactive</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            @if($category->products_count == 0)
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete category \'{{ addslashes($category->name) }}\'?')">
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
                        <i class="fas fa-tags fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">No categories found.</p>
                        @can('create categories')
                        <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add First Category
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">{{ $categories->total() }} categories total</small>
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
