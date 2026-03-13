@extends('layouts.dashboard')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
@php $permissions = $user->getAllPermissions()->sortBy('name'); @endphp

<div class="row g-3">

    {{-- ── Left Column: Avatar + Info ── --}}
    <div class="col-md-4">

        {{-- Profile Card --}}
        <div class="table-card mb-3">
            <div class="p-4 text-center">
                {{-- Avatar --}}
                <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,#bbf7d0,#16a34a);display:flex;align-items:center;justify-content:center;color:white;font-size:38px;font-weight:800;margin:0 auto 16px;box-shadow:0 8px 24px rgba(22,163,74,0.3);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1" style="font-size:20px;">{{ $user->name }}</h5>
                <p class="text-muted mb-3" style="font-size:14px;">{{ $user->email }}</p>

                {{-- Roles --}}
                <div class="mb-3">
                    @forelse($user->roles as $role)
                        <span class="badge bg-primary me-1" style="font-size:12px;padding:6px 12px;">{{ $role->name }}</span>
                    @empty
                        <span class="badge bg-secondary" style="font-size:12px;">No Role Assigned</span>
                    @endforelse
                </div>

                <a href="{{ route('profile.edit') }}" class="btn btn-primary w-100">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </a>
            </div>
            <div class="border-top px-4 py-3">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6;font-size:13px;">
                    <span class="text-muted">Account Created</span>
                    <span class="fw-semibold">{{ $user->created_at->format('d M Y') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6;font-size:13px;">
                    <span class="text-muted">Last Updated</span>
                    <span class="fw-semibold">{{ $user->updated_at->format('d M Y') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:13px;">
                    <span class="text-muted">Permissions</span>
                    <span class="badge" style="background:#f0fdf4;color:#166534;font-weight:700;">{{ $permissions->count() }}</span>
                </div>
            </div>
        </div>

        {{-- Permissions List --}}
        @if($permissions->count() > 0)
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-key me-2"></i>My Permissions</h6>
                <span class="badge bg-primary">{{ $permissions->count() }}</span>
            </div>
            <div class="p-3" style="max-height:300px;overflow-y:auto;">
                @foreach($permissions->groupBy(fn($p) => explode(' ', $p->name)[0] ?? 'other') as $module => $perms)
                <div class="mb-2">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin-bottom:4px;">{{ ucfirst($module) }}</div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($perms as $perm)
                        <span style="background:#f0fdf4;color:#166534;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">
                            {{ implode(' ', array_slice(explode(' ', $perm->name), 1)) }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── Right Column: Edit Info + Password ── --}}
    <div class="col-md-8">

        {{-- Edit Profile Info --}}
        <div class="table-card mb-3">
            <div class="table-card-header">
                <h6><i class="fas fa-user-edit me-2"></i>Account Information</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-lock me-2"></i>Change Password</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('profile.password.update') }}" method="POST" id="passwordForm">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                       name="current_password" id="current_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye" id="eye_current_password"></i>
                                </button>
                            </div>
                            @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       name="password" id="new_password" required oninput="checkStrength(this.value)">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye" id="eye_new_password"></i>
                                </button>
                            </div>
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            {{-- Password strength bar --}}
                            <div class="mt-2" id="strengthBar" style="display:none;">
                                <div style="background:#e5e7eb;border-radius:4px;height:5px;overflow:hidden;">
                                    <div id="strengthFill" style="height:100%;border-radius:4px;transition:width .3s,background .3s;width:0%;"></div>
                                </div>
                                <div style="font-size:11px;color:#6b7280;margin-top:3px;" id="strengthLabel"></div>
                            </div>
                            <div class="form-text">Minimum 8 characters</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control"
                                       name="password_confirmation" id="confirm_password" required oninput="checkMatch()">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye" id="eye_confirm_password"></i>
                                </button>
                            </div>
                            <div style="font-size:11px;margin-top:4px;" id="matchLabel"></div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Update Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eye   = document.getElementById('eye_' + fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        eye.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        eye.className = 'fas fa-eye';
    }
}

function checkStrength(value) {
    const bar   = document.getElementById('strengthBar');
    const fill  = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');

    if (!value) { bar.style.display = 'none'; return; }
    bar.style.display = 'block';

    let score = 0;
    if (value.length >= 8)  score++;
    if (value.length >= 12) score++;
    if (/[A-Z]/.test(value)) score++;
    if (/[0-9]/.test(value)) score++;
    if (/[^A-Za-z0-9]/.test(value)) score++;

    const levels = [
        { w:'20%', bg:'#ef4444', t:'Very Weak' },
        { w:'40%', bg:'#f97316', t:'Weak' },
        { w:'60%', bg:'#eab308', t:'Fair' },
        { w:'80%', bg:'#22c55e', t:'Strong' },
        { w:'100%', bg:'#16a34a', t:'Very Strong' },
    ];
    const lvl = levels[Math.min(score - 1, 4)] || levels[0];
    fill.style.width  = lvl.w;
    fill.style.background = lvl.bg;
    label.textContent = lvl.t;
    label.style.color = lvl.bg;
}

function checkMatch() {
    const pwd     = document.getElementById('new_password').value;
    const confirm = document.getElementById('confirm_password').value;
    const label   = document.getElementById('matchLabel');

    if (!confirm) { label.textContent = ''; return; }
    if (pwd === confirm) {
        label.textContent = '✓ Passwords match';
        label.style.color = '#16a34a';
    } else {
        label.textContent = '✗ Passwords do not match';
        label.style.color = '#ef4444';
    }
}

// Auto-open password section if there's an error
@if($errors->has('current_password') || $errors->has('password'))
    document.getElementById('current_password').focus();
@endif
</script>
@endpush
@endsection
