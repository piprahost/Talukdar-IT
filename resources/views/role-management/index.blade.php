@extends('layouts.dashboard')

@section('title', 'Role Management')
@section('page-title', 'Role Management')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-user-tag me-2"></i>All Roles</h6>
        @can('create roles')
        <a href="{{ route('role-management.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Role
        </a>
        @endcan
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role Name</th>
                    <th>Permissions</th>
                    <th>Users Count</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2" style="font-size: 14px; padding: 8px 12px;">
                                    <i class="fas fa-user-tag me-1"></i>{{ $role->name }}
                                </span>
                            </div>
                        </td>
                        <td>
                            @if($role->permissions->count() > 0)
                                <span class="badge bg-info">{{ $role->permissions->count() }} permission(s)</span>
                            @else
                                <span class="text-muted">No permissions</span>
                            @endif
                        </td>
                        <td>
                            @if($role->users_count > 0)
                                <span class="badge bg-warning">{{ $role->users_count }} user(s)</span>
                            @else
                                <span class="text-muted">0 users</span>
                            @endif
                        </td>
                        <td>{{ $role->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('role-management.show', $role) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('role-management.edit', $role) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('role-management.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" {{ $role->users_count > 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-user-tag fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">No roles found. <a href="{{ route('role-management.create') }}">Create the first role</a></p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($roles->hasPages())
        <div class="mt-4">
            {{ $roles->links() }}
        </div>
    @endif
</div>
@endsection

