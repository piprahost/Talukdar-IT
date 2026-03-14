@extends('layouts.dashboard')

@section('title', 'App Settings')
@section('page-title', 'App Settings')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Settings by category</h6>
    </div>
    <div class="p-4">
        <p class="text-muted mb-3">Select a category to edit settings.</p>
        <div class="row g-2">
            @foreach($categories as $slug => $cat)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('settings.app.edit', ['category' => $slug]) }}" class="btn btn-outline-primary w-100 text-start">
                    <i class="{{ $cat['icon'] }} me-2"></i>{{ $cat['label'] }}
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
