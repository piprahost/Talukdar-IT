@extends('layouts.dashboard')

@section('title', 'Edit Brand')
@section('page-title', 'Edit Brand')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-certificate me-2"></i>Edit Brand</h6>
                <a href="{{ route('brands.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            <form action="{{ route('brands.update', $brand) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3"><label for="name" class="form-label">Brand Name <span class="text-danger">*</span></label><input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $brand->name) }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label for="description" class="form-label">Description</label><textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $brand->description) }}</textarea></div>
                <div class="mb-3"><label for="website" class="form-label">Website</label><input type="url" class="form-control" id="website" name="website" value="{{ old('website', $brand->website) }}"></div>
                <div class="mb-3"><div class="form-check"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $brand->is_active) ? 'checked' : '' }}><label class="form-check-label" for="is_active">Active</label></div></div>
                <div class="d-flex justify-content-between"><a href="{{ route('brands.index') }}" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update Brand</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

