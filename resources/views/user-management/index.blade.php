@extends('layouts.dashboard')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-users-cog me-2"></i>System Users</h6>
        @can('create users')
        <a href="{{ route('user-management.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Add New User
        </a>
        @endcan
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th class="text-center">Permissions</th>
                    <th>Joined</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php $permCount = $user->getAllPermissions()->count(); @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar" style="width:38px;height:38px;font-size:15px;flex-shrink:0;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:14px;">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                <span style="font-size:10px;background:#f0fdf4;color:#166534;padding:1px 6px;border-radius:10px;font-weight:600;">You</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;">{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary me-1" style="font-size:11px;">{{ $role->name }}</span>
                        @endforeach
                        @if($user->roles->isEmpty())
                            <span class="text-muted" style="font-size:12px;">No roles assigned</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($permCount > 0)
                            <span class="badge" style="background:#f0fdf4;color:#166534;font-weight:700;font-size:12px;">{{ $permCount }}</span>
                        @else
                            <span class="text-muted" style="font-size:12px;">0</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#9ca3af;">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            @can('view users')
                            <a href="{{ route('user-management.show', $user) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                            @endcan
                            @can('edit users')
                            <a href="{{ route('user-management.edit', $user) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            @endcan
                            @can('delete users')
                            @if($user->id !== auth()->id())
                            <form action="{{ route('user-management.destroy', $user) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete user {{ addslashes($user->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">No users found.</p>
                        @can('create users')
                        <a href="{{ route('user-management.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add First User
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">{{ $users->total() }} users total</small>
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
