@extends('layouts.dashboard')

@section('title', 'Edit Supplier')
@section('page-title', 'Edit Supplier')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-truck me-2"></i>Edit Supplier</h6>
                <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3"><label for="name" class="form-label">Supplier Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="name" name="name" value="{{ old('name', $supplier->name) }}" required></div>
                <div class="mb-3"><label for="company_name" class="form-label">Company Name</label><input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $supplier->company_name) }}"></div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" value="{{ old('email', $supplier->email) }}"></div>
                    <div class="col-md-6 mb-3"><label for="phone" class="form-label">Phone</label><input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}"></div>
                </div>
                <div class="mb-3"><label for="mobile" class="form-label">Mobile</label><input type="text" class="form-control" id="mobile" name="mobile" value="{{ old('mobile', $supplier->mobile) }}"></div>
                <div class="mb-3"><label for="address" class="form-label">Address</label><textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $supplier->address) }}</textarea></div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="city" class="form-label">City</label><input type="text" class="form-control" id="city" name="city" value="{{ old('city', $supplier->city) }}"></div>
                    <div class="col-md-6 mb-3"><label for="country" class="form-label">Country</label><input type="text" class="form-control" id="country" name="country" value="{{ old('country', $supplier->country) }}"></div>
                </div>
                <div class="mb-3"><label for="tax_id" class="form-label">Tax ID</label><input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ old('tax_id', $supplier->tax_id) }}"></div>
                <div class="mb-3"><label for="notes" class="form-label">Notes</label><textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes', $supplier->notes) }}</textarea></div>
                <div class="mb-3"><div class="form-check"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}><label class="form-check-label" for="is_active">Active</label></div></div>
                <div class="d-flex justify-content-between"><a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update Supplier</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

