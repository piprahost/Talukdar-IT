@extends('layouts.dashboard')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category')

@section('content')
<form action="{{ route('categories.update', $category) }}" method="POST">
@csrf
@method('PUT')
<div class="row">
    <div class="col-md-8">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-tags me-2"></i>Category Details</h6>
                <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            <div class="p-4">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required placeholder="e.g. Laptops, Accessories">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Brief description of this category">{{ old('description', $category->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-cog me-2"></i>Settings</h6>
            </div>
            <div class="p-4">
                <div class="mb-3">
                    <label for="sort_order" class="form-label">Sort Order</label>
                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Lower numbers appear first in lists.</small>
                </div>
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    <small class="text-muted">Inactive categories are hidden from product selection.</small>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Category</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection
