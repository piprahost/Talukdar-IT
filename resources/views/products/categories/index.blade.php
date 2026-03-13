@extends('layouts.dashboard')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-tags me-2"></i>All Categories</h6>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Category
        </a>
    </div>
    
    <form method="GET" action="{{ route('categories.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-6 mb-2">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search categories...">
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-select" name="status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <button type="submit" class="btn btn-outline-primary w-100">Search</button>
            </div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>{{ $category->description ? substr($category->description, 0, 50) . '...' : 'N/A' }}</td>
                        <td><span class="badge bg-info">{{ $category->products_count }}</span></td>
                        <td>
                            <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('categories.show', $category) }}" class="btn btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No categories found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{ $categories->links() }}
</div>
@endsection

