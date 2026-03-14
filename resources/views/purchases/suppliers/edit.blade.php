@extends('layouts.dashboard')

@section('title', 'Edit Supplier')
@section('page-title', 'Edit Supplier')

@section('content')
<form action="{{ route('suppliers.update', $supplier) }}" method="POST">
@csrf
@method('PUT')
<div class="row">
    <div class="col-md-8">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-truck me-2"></i>Supplier Information</h6>
                <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            <div class="p-4">
                <div class="mb-3">
                    <label for="name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $supplier->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $supplier->company_name) }}" placeholder="Optional">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $supplier->email) }}" placeholder="email@example.com">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone text-muted"></i></span>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="mobile" class="form-label">Mobile</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-mobile-alt text-muted"></i></span>
                        <input type="text" class="form-control" id="mobile" name="mobile" value="{{ old('mobile', $supplier->mobile) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2" placeholder="Street, area">{{ old('address', $supplier->address) }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $supplier->city) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="country" class="form-label">Country</label>
                        <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $supplier->country) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="tax_id" class="form-label">Tax ID</label>
                    <input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ old('tax_id', $supplier->tax_id) }}" placeholder="Optional">
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
                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Internal notes">{{ old('notes', $supplier->notes) }}</textarea>
                </div>
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    <small class="text-muted">Inactive suppliers are hidden from selection in new purchase orders.</small>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Supplier</button>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection
