@extends('layouts.dashboard')

@section('title', 'User Details')
@section('page-title', 'User Details: ' . $user->name)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-user me-2"></i>User Information</h6>
                <div>
                    <a href="{{ route('user-management.edit', $user) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('user-management.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12 text-center mb-4">
                    <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                </div>
            </div>
            
            <hr>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>User ID:</strong>
                </div>
                <div class="col-md-8">
                    {{ $user->id }}
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Email:</strong>
                </div>
                <div class="col-md-8">
                    {{ $user->email }}
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Created At:</strong>
                </div>
                <div class="col-md-8">
                    {{ $user->created_at->format('F d, Y h:i A') }}
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Last Updated:</strong>
                </div>
                <div class="col-md-8">
                    {{ $user->updated_at->format('F d, Y h:i A') }}
                </div>
            </div>
            
            <hr>
            
            <div class="mb-3">
                <strong>Assigned Roles:</strong>
                <div class="mt-2">
                    @forelse($user->roles as $role)
                        <span class="badge bg-primary me-1 mb-1" style="font-size: 14px; padding: 8px 12px;">
                            <i class="fas fa-user-tag me-1"></i>{{ $role->name }}
                        </span>
                    @empty
                        <span class="text-muted">No roles assigned</span>
                    @endforelse
                </div>
            </div>
            
            <hr>
            
            <div class="mb-3">
                <strong>All Permissions (via roles):</strong>
                <small class="text-muted d-block mb-2">Permissions inherited from assigned roles</small>
                <div class="mt-2">
                    @php
                        $allPermissions = $user->getAllPermissions();
                    @endphp
                    @forelse($allPermissions as $permission)
                        <span class="badge bg-success me-1 mb-1" style="font-size: 12px; padding: 5px 10px;">
                            {{ $permission->name }}
                        </span>
                    @empty
                        <span class="text-muted">No permissions</span>
                    @endforelse
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <form action="{{ route('user-management.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        <i class="fas fa-trash me-2"></i>Delete User
                    </button>
                </form>
                <a href="{{ route('user-management.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

