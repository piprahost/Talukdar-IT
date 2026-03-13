@extends('layouts.dashboard')

@section('title', 'Create Customer')
@section('page-title', 'Create Customer')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-users me-2"></i>Create Customer</h6>
                <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="mb-3"><label for="name" class="form-label">Customer Name <span class="text-danger">*</span></label><input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"></div>
                    <div class="col-md-6 mb-3"><label for="phone" class="form-label">Phone</label><input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}"></div>
                </div>
                <div class="mb-3"><label for="mobile" class="form-label">Mobile</label><input type="text" class="form-control" id="mobile" name="mobile" value="{{ old('mobile') }}"></div>
                <div class="mb-3"><label for="address" class="form-label">Address</label><textarea class="form-control" id="address" name="address" rows="2">{{ old('address') }}</textarea></div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="city" class="form-label">City</label><input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}"></div>
                    <div class="col-md-6 mb-3"><label for="country" class="form-label">Country</label><input type="text" class="form-control" id="country" name="country" value="{{ old('country', 'Bangladesh') }}"></div>
                </div>
                <div class="mb-3"><label for="tax_id" class="form-label">Tax ID</label><input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ old('tax_id') }}"></div>
                <div class="mb-3"><label for="notes" class="form-label">Notes</label><textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea></div>
                <div class="mb-3"><div class="form-check"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}><label class="form-check-label" for="is_active">Active</label></div></div>
                <div class="d-flex justify-content-between"><a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Create Customer</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

