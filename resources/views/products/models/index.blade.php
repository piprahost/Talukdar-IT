@extends('layouts.dashboard')

@section('title', 'Product Models')
@section('page-title', 'Product Models')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-layer-group me-2"></i>All Product Models</h6>
        <a href="{{ route('product-models.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Model</a>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-2"><input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search..."></div>
            <div class="col-md-3 mb-2"><select class="form-select" name="brand_id" onchange="this.form.submit()"><option value="">All Brands</option>@foreach($brands as $b)<option value="{{ $b->id }}" {{ request('brand_id')==$b->id?'selected':'' }}>{{ $b->name }}</option>@endforeach</select></div>
            <div class="col-md-2 mb-2"><select class="form-select" name="status" onchange="this.form.submit()"><option value="">All</option><option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option></select></div>
            <div class="col-md-3 mb-2"><button type="submit" class="btn btn-outline-primary w-100">Search</button></div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Name</th><th>Brand</th><th>Description</th><th>Products</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($models as $model)
                    <tr>
                        <td><strong>{{ $model->name }}</strong></td>
                        <td>{{ $model->brand->name ?? 'N/A' }}</td>
                        <td>{{ $model->description ? substr($model->description, 0, 50) . '...' : 'N/A' }}</td>
                        <td><span class="badge bg-info">{{ $model->products_count }}</span></td>
                        <td><span class="badge {{ $model->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $model->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('product-models.show', $model) }}" class="btn btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('product-models.edit', $model) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('product-models.destroy', $model) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No models found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $models->links() }}
</div>
@endsection

