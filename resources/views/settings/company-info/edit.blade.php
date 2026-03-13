@extends('layouts.dashboard')

@section('title', 'Company Settings')
@section('page-title', 'Company Settings')

@section('content')
<form action="{{ route('company-info.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-3">

        {{-- Left Column --}}
        <div class="col-md-8">

            {{-- Basic Info --}}
            <div class="table-card mb-3">
                <div class="table-card-header">
                    <h6><i class="fas fa-building me-2"></i>Company Identity</h6>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                   name="company_name" value="{{ old('company_name', $company->company_name ?? '') }}" required
                                   placeholder="e.g. Talukdar IT">
                            @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Service Center Name</label>
                            <input type="text" class="form-control @error('service_center_name') is-invalid @enderror"
                                   name="service_center_name" value="{{ old('service_center_name', $company->service_center_name ?? '') }}"
                                   placeholder="e.g. Talukdar IT Service Center">
                            @error('service_center_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Appears on service memos</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Street Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      name="address" rows="2" placeholder="House #, Road #, Area...">{{ old('address', $company->address ?? '') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                   name="city" value="{{ old('city', $company->city ?? 'Dhaka') }}" required>
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Country <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror"
                                   name="country" value="{{ old('country', $company->country ?? 'Bangladesh') }}" required>
                            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Terms & Conditions --}}
            <div class="table-card">
                <div class="table-card-header">
                    <h6><i class="fas fa-file-contract me-2"></i>Terms & Conditions</h6>
                    <small class="text-muted">Printed on service memos</small>
                </div>
                <div class="p-4">
                    <textarea class="form-control @error('terms_and_conditions') is-invalid @enderror"
                              name="terms_and_conditions" rows="10"
                              placeholder="1. Items are repaired at customer's risk...&#10;2. Service charges must be paid before delivery...&#10;3. Unclaimed items after 30 days will be disposed...">{{ old('terms_and_conditions', $company->terms_and_conditions ?? '') }}</textarea>
                    @error('terms_and_conditions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text mt-2">
                        <i class="fas fa-info-circle me-1"></i>Use one line per condition. This text appears at the bottom of printed service memos.
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-md-4">

            {{-- Contact Info --}}
            <div class="table-card mb-3">
                <div class="table-card-header">
                    <h6><i class="fas fa-phone me-2"></i>Contact Information</h6>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   name="phone" value="{{ old('phone', $company->phone ?? '') }}" placeholder="+880 1XXX XXXXXX">
                        </div>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email', $company->email ?? '') }}" placeholder="info@company.com">
                        </div>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Website</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                            <input type="url" class="form-control @error('website') is-invalid @enderror"
                                   name="website" value="{{ old('website', $company->website ?? '') }}" placeholder="https://www.company.com">
                        </div>
                        @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Preview Card --}}
            <div class="table-card mb-3">
                <div class="table-card-header">
                    <h6><i class="fas fa-eye me-2"></i>Document Preview</h6>
                </div>
                <div class="p-4">
                    <div class="p-3 rounded" style="background:#f9fafb;border:1px solid #e5e7eb;font-size:12px;">
                        <div style="font-size:16px;font-weight:800;color:#16a34a;text-transform:uppercase;">
                            {{ $company->company_name ?? 'COMPANY NAME' }}
                        </div>
                        @if($company->service_center_name ?? false)
                        <div style="font-size:11px;color:#15803d;text-transform:uppercase;letter-spacing:1px;">
                            {{ $company->service_center_name }}
                        </div>
                        @endif
                        <div style="color:#6b7280;margin-top:6px;line-height:1.5;">
                            {{ $company->address ?? '' }}
                            @if($company->city ?? false), {{ $company->city }}@endif
                            @if($company->country ?? false), {{ $company->country }}@endif
                        </div>
                        @if($company->phone ?? false)
                        <div style="color:#6b7280;margin-top:4px;">📞 {{ $company->phone }}</div>
                        @endif
                        @if($company->email ?? false)
                        <div style="color:#6b7280;">✉ {{ $company->email }}</div>
                        @endif
                    </div>
                    <div class="form-text mt-2">This is how your company info appears on printed documents.</div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Save Company Settings
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
            </div>
        </div>

    </div>
</form>
@endsection
