@extends('layouts.dashboard')

@section('title', 'Create Customer')
@section('page-title', 'Create Customer')

@section('content')
<form action="{{ route('customers.store') }}" method="POST">
@csrf
<div class="row">
    <div class="col-md-8">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-users me-2"></i>Customer Information</h6>
                <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="email@example.com">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone text-muted"></i></span>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="mobile" class="form-label">Mobile</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-mobile-alt text-muted"></i></span>
                            <input type="text" class="form-control" id="mobile" name="mobile" value="{{ old('mobile') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Street, area">{{ old('address') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control" id="country" name="country" value="{{ old('country', 'Bangladesh') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tax_id" class="form-label">Tax ID</label>
                        <input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ old('tax_id') }}" placeholder="Optional">
                    </div>
                </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-cog me-2"></i>Settings & Notes</h6>
            </div>
            <div class="p-4">
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Internal notes">{{ old('notes') }}</textarea>
                </div>
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    <small class="text-muted">Inactive customers are hidden from selection in new sales.</small>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Create Customer</button>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection
