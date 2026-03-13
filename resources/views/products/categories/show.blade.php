@extends('layouts.dashboard')

@section('title', 'Category Details')
@section('page-title', 'Category Details')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-tags me-2"></i>{{ $category->name }}</h6>
                <div>
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                    <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <div class="p-3">
                <table class="table table-borderless">
                    <tr><th>Name:</th><td>{{ $category->name }}</td></tr>
                    <tr><th>Description:</th><td>{{ $category->description ?? 'N/A' }}</td></tr>
                    <tr><th>Status:</th><td><span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                    <tr><th>Products Count:</th><td><span class="badge bg-info">{{ $category->products_count }}</span></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

