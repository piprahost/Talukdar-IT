@extends('layouts.dashboard')

@section('title', 'Brands')
@section('page-title', 'Brands')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-certificate me-2"></i>All Brands</h6>
        <a href="{{ route('brands.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Brand</a>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-6 mb-2"><input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search..."></div>
            <div class="col-md-3 mb-2"><select class="form-select" name="status" onchange="this.form.submit()"><option value="">All</option><option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option></select></div>
            <div class="col-md-3 mb-2"><button type="submit" class="btn btn-outline-primary w-100">Search</button></div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Name</th><th>Description</th><th>Products</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($brands as $brand)
                    <tr>
                        <td><strong>{{ $brand->name }}</strong></td>
                        <td>{{ $brand->description ? substr($brand->description, 0, 50) . '...' : 'N/A' }}</td>
                        <td><span class="badge bg-info">{{ $brand->products_count }}</span></td>
                        <td><span class="badge {{ $brand->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $brand->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('brands.show', $brand) }}" class="btn btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('brands.edit', $brand) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No brands found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $brands->links() }}
</div>
@endsection

