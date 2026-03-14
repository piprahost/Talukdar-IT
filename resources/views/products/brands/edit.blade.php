@extends('layouts.dashboard')

@section('title', 'Edit Brand')
@section('page-title', 'Edit Brand')

@section('content')
<form action="{{ route('brands.update', $brand) }}" method="POST">
@csrf
@method('PUT')
<div class="row">
    <div class="col-md-8">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-certificate me-2"></i>Brand Information</h6>
                <a href="{{ route('brands.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            <div class="p-4">
                <div class="mb-3">
                    <label for="name" class="form-label">Brand Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $brand->name) }}" required placeholder="e.g. Dell, HP, Lenovo">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief description of the brand">{{ old('description', $brand->description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="website" class="form-label">Website</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-globe text-muted"></i></span>
                        <input type="url" class="form-control" id="website" name="website" value="{{ old('website', $brand->website) }}" placeholder="https://">
                    </div>
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
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $brand->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    <small class="text-muted">Inactive brands are hidden from product selection.</small>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Brand</button>
                    <a href="{{ route('brands.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection
