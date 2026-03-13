@extends('layouts.dashboard')

@section('title', 'Edit Model')
@section('page-title', 'Edit Product Model')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-layer-group me-2"></i>Edit Model</h6>
                <a href="{{ route('product-models.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            <form action="{{ route('product-models.update', $productModel) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3"><label for="brand_id" class="form-label">Brand <span class="text-danger">*</span></label><select class="form-select" id="brand_id" name="brand_id" required><option value="">Select Brand</option>@foreach($brands as $brand)<option value="{{ $brand->id }}" {{ old('brand_id', $productModel->brand_id)==$brand->id?'selected':'' }}>{{ $brand->name }}</option>@endforeach</select></div>
                <div class="mb-3"><label for="name" class="form-label">Model Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="name" name="name" value="{{ old('name', $productModel->name) }}" required></div>
                <div class="mb-3"><label for="description" class="form-label">Description</label><textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $productModel->description) }}</textarea></div>
                <div class="mb-3"><div class="form-check"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $productModel->is_active) ? 'checked' : '' }}><label class="form-check-label" for="is_active">Active</label></div></div>
                <div class="d-flex justify-content-between"><a href="{{ route('product-models.index') }}" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update Model</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

