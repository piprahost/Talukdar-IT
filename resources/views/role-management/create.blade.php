@extends('layouts.dashboard')

@section('title', 'Create Role')
@section('page-title', 'Create New Role')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-user-tag me-2"></i>Create New Role</h6>
                <a href="{{ route('role-management.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
            
            <form action="{{ route('role-management.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., Editor, Viewer, Manager" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Enter a unique role name</small>
                </div>
                
                <hr class="my-4">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Assign Permissions</label>
                    <small class="text-muted d-block mb-3">Select the permissions this role should have.</small>
                    
                    @foreach($groupedPermissions as $group => $groupPermissions)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong class="text-capitalize">{{ $group }}</strong>
                                <button type="button" class="btn btn-sm btn-link float-end" onclick="toggleGroup('{{ $group }}')">
                                    <i class="fas fa-chevron-down" id="icon_{{ $group }}"></i>
                                </button>
                            </div>
                            <div class="card-body" id="group_{{ $group }}">
                                <div class="row">
                                    @foreach($groupPermissions as $permission)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ old('permissions') && in_array($permission->id, old('permissions')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPermissions()">
                            <i class="fas fa-check-double me-2"></i>Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllPermissions()">
                            <i class="fas fa-times me-2"></i>Deselect All
                        </button>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('role-management.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleGroup(group) {
    const groupDiv = document.getElementById('group_' + group);
    const icon = document.getElementById('icon_' + group);
    
    if (groupDiv.style.display === 'none') {
        groupDiv.style.display = 'block';
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    } else {
        groupDiv.style.display = 'none';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    }
}

function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
@endpush
@endsection

