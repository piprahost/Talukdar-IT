@extends('layouts.dashboard')

@section('title', 'Role Management')
@section('page-title', 'Role Management')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-user-tag me-2"></i>All Roles</h6>
        @can('create roles')
        <a href="{{ route('role-management.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-2"></i>Add New Role
        </a>
        @endcan
    </div>

    <div class="filter-wrapper">
        <form method="GET" action="{{ route('role-management.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label small text-muted mb-0">Search by name</label>
                    <input type="text" class="form-control form-control-sm" id="search" name="search" value="{{ request('search') }}" placeholder="Role name...">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i> Search</button>
                    @if(request('search'))
                    <a href="{{ route('role-management.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role Name</th>
                    <th>Permissions</th>
                    <th>Users</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td class="text-muted">{{ $role->id }}</td>
                        <td>
                            <span class="badge bg-primary" style="font-size: 13px; padding: 6px 12px;">
                                <i class="fas fa-user-tag me-1"></i>{{ $role->name }}
                            </span>
                        </td>
                        <td>
                            @if($role->permissions->count() > 0)
                                <span class="badge" style="background:#f0fdf4;color:#166534;font-weight:600;">{{ $role->permissions->count() }} permission(s)</span>
                            @else
                                <span class="text-muted small">No permissions</span>
                            @endif
                        </td>
                        <td>
                            @if($role->users_count > 0)
                                <span class="badge bg-warning text-dark">{{ $role->users_count }} user(s)</span>
                            @else
                                <span class="text-muted small">0 users</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $role->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('role-management.show', $role) }}" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('role-management.edit', $role) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('role-management.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete" {{ $role->users_count > 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-user-tag fa-3x text-muted mb-3 d-block opacity-50"></i>
                            <p class="text-muted mb-2">No roles found.</p>
                            @if(request('search'))
                                <a href="{{ route('role-management.index') }}" class="btn btn-outline-secondary btn-sm">Clear search</a>
                            @else
                                @can('create roles')
                                <a href="{{ route('role-management.create') }}" class="btn btn-primary btn-sm">Create the first role</a>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($roles->hasPages())
        <div class="p-3 border-top">
            {{ $roles->links() }}
        </div>
    @endif
</div>
@endsection
