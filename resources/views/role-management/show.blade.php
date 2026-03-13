@extends('layouts.dashboard')

@section('title', 'Role Details')
@section('page-title', 'Role Details: ' . $role->name)

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-user-tag me-2"></i>Role Information</h6>
                <div>
                    <a href="{{ route('role-management.edit', $role) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('role-management.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12 text-center mb-4">
                    <span class="badge bg-primary" style="font-size: 24px; padding: 15px 25px;">
                        <i class="fas fa-user-tag me-2"></i>{{ $role->name }}
                    </span>
                </div>
            </div>
            
            <hr>
            
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Role ID:</strong>
                </div>
                <div class="col-md-9">
                    {{ $role->id }}
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Role Name:</strong>
                </div>
                <div class="col-md-9">
                    <span class="badge bg-primary">{{ $role->name }}</span>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Users with this Role:</strong>
                </div>
                <div class="col-md-9">
                    <span class="badge bg-warning">{{ $usersWithRole->count() }} user(s)</span>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Created At:</strong>
                </div>
                <div class="col-md-9">
                    {{ $role->created_at->format('F d, Y h:i A') }}
                </div>
            </div>
            
            <hr>
            
            <div class="mb-3">
                <strong>Assigned Permissions ({{ $role->permissions->count() }}):</strong>
                <div class="mt-2">
                    @forelse($role->permissions as $permission)
                        <span class="badge bg-success me-1 mb-1" style="font-size: 12px; padding: 5px 10px;">
                            {{ $permission->name }}
                        </span>
                    @empty
                        <span class="text-muted">No permissions assigned</span>
                    @endforelse
                </div>
            </div>
            
            @if($usersWithRole->count() > 0)
                <hr>
                <div class="mb-3">
                    <strong>Users with this Role:</strong>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usersWithRole as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            
            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <form action="{{ route('role-management.destroy', $role) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" {{ $usersWithRole->count() > 0 ? 'disabled' : '' }}>
                        <i class="fas fa-trash me-2"></i>Delete Role
                    </button>
                    @if($usersWithRole->count() > 0)
                        <small class="text-muted d-block mt-1">{{ $usersWithRole->count() }} user(s) have this role. Remove the role from all users first.</small>
                    @endif
                </form>
                <a href="{{ route('role-management.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

