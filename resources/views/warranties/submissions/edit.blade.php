@extends('layouts.dashboard')

@section('title', 'Update Warranty Submission')
@section('page-title', 'Update Warranty Submission')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-file-alt me-2"></i>Update Warranty Submission: {{ $warrantySubmission->memo_number }}</h6>
                <a href="{{ route('warranty-submissions.show', $warrantySubmission) }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
            
            <form action="{{ route('warranty-submissions.update', $warrantySubmission) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="pending" {{ old('status', $warrantySubmission->status)=='pending'?'selected':'' }}>Pending</option>
                                <option value="received" {{ old('status', $warrantySubmission->status)=='received'?'selected':'' }}>Received</option>
                                <option value="in_progress" {{ old('status', $warrantySubmission->status)=='in_progress'?'selected':'' }}>In Progress</option>
                                <option value="completed" {{ old('status', $warrantySubmission->status)=='completed'?'selected':'' }}>Completed</option>
                                <option value="returned" {{ old('status', $warrantySubmission->status)=='returned'?'selected':'' }}>Returned</option>
                                <option value="cancelled" {{ old('status', $warrantySubmission->status)=='cancelled'?'selected':'' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="service_charge" class="form-label">Service Charge (BDT)</label>
                            <input type="number" step="0.01" class="form-control" id="service_charge" name="service_charge" 
                                   value="{{ old('service_charge', $warrantySubmission->service_charge) }}">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="assigned_to" class="form-label">Assign To</label>
                            <select class="form-select" id="assigned_to" name="assigned_to">
                                <option value="">Unassigned</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $warrantySubmission->assigned_to) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="expected_completion_date" class="form-label">Expected Completion Date</label>
                            <input type="date" class="form-control" id="expected_completion_date" name="expected_completion_date" 
                                   value="{{ old('expected_completion_date', $warrantySubmission->expected_completion_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="completion_date" class="form-label">Completion Date</label>
                            <input type="date" class="form-control" id="completion_date" name="completion_date" 
                                   value="{{ old('completion_date', $warrantySubmission->completion_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="return_date" class="form-label">Return Date</label>
                            <input type="date" class="form-control" id="return_date" name="return_date" 
                                   value="{{ old('return_date', $warrantySubmission->return_date?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="service_notes" class="form-label">Service Notes</label>
                        <textarea class="form-control" id="service_notes" name="service_notes" rows="4">{{ old('service_notes', $warrantySubmission->service_notes) }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="internal_notes" class="form-label">Internal Notes</label>
                        <textarea class="form-control" id="internal_notes" name="internal_notes" rows="3">{{ old('internal_notes', $warrantySubmission->internal_notes) }}</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('warranty-submissions.show', $warrantySubmission) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Submission
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

