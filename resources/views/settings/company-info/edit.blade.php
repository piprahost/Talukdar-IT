@extends('layouts.dashboard')

@section('title', 'Company Information')
@section('page-title', 'Company Information Settings')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-building me-2"></i>Company Information</h6>
                <small class="text-muted">Update your company information that appears on service memos and documents</small>
            </div>
            
            <form action="{{ route('company-info.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name', $company->company_name ?? '') }}" required>
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="service_center_name" class="form-label">Service Center Name</label>
                            <input type="text" class="form-control @error('service_center_name') is-invalid @enderror" id="service_center_name" name="service_center_name" value="{{ old('service_center_name', $company->service_center_name ?? '') }}">
                            @error('service_center_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Address Information -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h6>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">Street Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" placeholder="Enter full street address...">{{ old('address', $company->address ?? '') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $company->city ?? 'Dhaka') }}" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country', $company->country ?? 'Bangladesh') }}" required>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Contact Information -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-phone me-2"></i>Contact Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $company->phone ?? '') }}" placeholder="+880 1XXX XXXXXX">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $company->email ?? '') }}" placeholder="info@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $company->website ?? '') }}" placeholder="https://www.example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Terms and Conditions -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-file-contract me-2"></i>Terms and Conditions</h6>
                    <div class="mb-3">
                        <label for="terms_and_conditions" class="form-label">Terms & Conditions (for Service Memos)</label>
                        <textarea class="form-control @error('terms_and_conditions') is-invalid @enderror" id="terms_and_conditions" name="terms_and_conditions" rows="8" placeholder="Enter terms and conditions that will appear on service memos...">{{ old('terms_and_conditions', $company->terms_and_conditions ?? '') }}</textarea>
                        @error('terms_and_conditions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">This text will appear on printed service memos. Use line breaks for each condition.</small>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Company Information
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

