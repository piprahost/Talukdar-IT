@extends('layouts.dashboard')

@section('title', 'Brand Details')
@section('page-title', 'Brand Details')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-certificate me-2"></i>{{ $brand->name }}</h6>
                <div><a href="{{ route('brands.edit', $brand) }}" class="btn btn-sm btn-warning me-2">Edit</a><a href="{{ route('brands.index') }}" class="btn btn-sm btn-outline-secondary">Back</a></div>
            </div>
            <div class="p-3">
                <table class="table table-borderless">
                    <tr><th>Name:</th><td>{{ $brand->name }}</td></tr>
                    <tr><th>Description:</th><td>{{ $brand->description ?? 'N/A' }}</td></tr>
                    <tr><th>Website:</th><td>{{ $brand->website ?? 'N/A' }}</td></tr>
                    <tr><th>Status:</th><td><span class="badge {{ $brand->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $brand->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                    <tr><th>Products:</th><td><span class="badge bg-info">{{ $brand->products_count }}</span></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

