@extends('layouts.dashboard')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-user me-2"></i>Profile Information</h6>
                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </a>
            </div>
            
            <div class="p-4">
                <div class="row mb-4">
                    <div class="col-md-3 text-center mb-3">
                        <div class="profile-avatar-large mb-3">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-2">{{ $user->email }}</p>
                        @if($user->roles->count() > 0)
                            <div>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-md-9">
                        <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i>Account Details</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 200px;">Full Name:</th>
                                <td><strong>{{ $user->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Email Address:</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th>Roles:</th>
                                <td>
                                    @if($user->roles->count() > 0)
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No roles assigned</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Account Created:</th>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $user->updated_at->format('M d, Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- Password Change Section -->
                <div class="mt-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-lock me-2"></i>Change Password</h6>
                    <form action="{{ route('profile.password.update') }}" method="POST" id="passwordForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" 
                                           name="current_password" 
                                           required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Minimum 8 characters</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Password
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: bold;
    margin: 0 auto;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}
</style>
@endsection

