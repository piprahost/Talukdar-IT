@extends('layouts.dashboard')

@section('title', 'Edit User')
@section('page-title', 'Edit User: ' . $user->name)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-user-edit me-2"></i>Edit User</h6>
                <a href="{{ route('user-management.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
            <form action="{{ route('user-management.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" oninput="checkStrength(this.value); checkMatch();">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')" title="Show/Hide">
                                    <i class="fas fa-eye" id="eye_password"></i>
                                </button>
                            </div>
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div class="mt-2" id="strengthBar" style="display:none;">
                                <div style="background:#e5e7eb;border-radius:4px;height:5px;overflow:hidden;">
                                    <div id="strengthFill" style="height:100%;border-radius:4px;transition:width .3s,background .3s;width:0%;"></div>
                                </div>
                                <div style="font-size:11px;color:#6b7280;margin-top:3px;" id="strengthLabel"></div>
                            </div>
                            <small class="text-muted">Leave blank to keep current password. Minimum 8 characters.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" oninput="checkMatch()">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')" title="Show/Hide">
                                    <i class="fas fa-eye" id="eye_password_confirmation"></i>
                                </button>
                            </div>
                            <div style="font-size:11px;margin-top:4px;" id="matchLabel"></div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Assign Roles</label>
                        <div class="row">
                            @foreach($roles as $role)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}"
                                            {{ (old('roles') ? in_array($role->id, old('roles')) : $user->hasRole($role->name)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            <strong>{{ $role->name }}</strong>
                                            <small class="text-muted d-block">{{ $role->permissions->count() }} permission(s)</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('user-management.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(fieldId) {
    var field = document.getElementById(fieldId);
    var eye   = document.getElementById('eye_' + fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        eye.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        eye.className = 'fas fa-eye';
    }
}
function checkStrength(value) {
    var bar = document.getElementById('strengthBar');
    var fill = document.getElementById('strengthFill');
    var label = document.getElementById('strengthLabel');
    if (!value) { bar.style.display = 'none'; return; }
    bar.style.display = 'block';
    var score = 0;
    if (value.length >= 8)  score++;
    if (value.length >= 12) score++;
    if (/[A-Z]/.test(value)) score++;
    if (/[0-9]/.test(value)) score++;
    if (/[^A-Za-z0-9]/.test(value)) score++;
    var levels = [
        { w:'20%', bg:'#ef4444', t:'Very Weak' },
        { w:'40%', bg:'#f97316', t:'Weak' },
        { w:'60%', bg:'#eab308', t:'Fair' },
        { w:'80%', bg:'#22c55e', t:'Strong' },
        { w:'100%', bg:'#16a34a', t:'Very Strong' }
    ];
    var lvl = levels[Math.min(score - 1, 4)] || levels[0];
    fill.style.width = lvl.w;
    fill.style.background = lvl.bg;
    label.textContent = lvl.t;
    label.style.color = lvl.bg;
}
function checkMatch() {
    var pwd = document.getElementById('password').value;
    var confirm = document.getElementById('password_confirmation').value;
    var label = document.getElementById('matchLabel');
    if (!confirm) { label.textContent = ''; return; }
    if (pwd === confirm) {
        label.textContent = '✓ Passwords match';
        label.style.color = '#16a34a';
    } else {
        label.textContent = '✗ Passwords do not match';
        label.style.color = '#ef4444';
    }
}
</script>
@endpush
@endsection
