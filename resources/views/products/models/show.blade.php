@extends('layouts.dashboard')

@section('title', 'Model Details')
@section('page-title', 'Model Details')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-layer-group me-2"></i>{{ $productModel->name }}</h6>
                <div><a href="{{ route('product-models.edit', $productModel) }}" class="btn btn-sm btn-warning me-2">Edit</a><a href="{{ route('product-models.index') }}" class="btn btn-sm btn-outline-secondary">Back</a></div>
            </div>
            <div class="p-3">
                <table class="table table-borderless">
                    <tr><th>Name:</th><td>{{ $productModel->name }}</td></tr>
                    <tr><th>Brand:</th><td>{{ $productModel->brand->name ?? 'N/A' }}</td></tr>
                    <tr><th>Description:</th><td>{{ $productModel->description ?? 'N/A' }}</td></tr>
                    <tr><th>Status:</th><td><span class="badge {{ $productModel->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $productModel->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                    <tr><th>Products:</th><td><span class="badge bg-info">{{ $productModel->products_count }}</span></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

