@extends('layouts.dashboard')

@section('title', 'Warranty Submission Details')
@section('page-title', 'Warranty Submission Details')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-file-alt me-2"></i>Memo: {{ $warrantySubmission->memo_number }}</h6>
                <div>
                    <a href="{{ route('warranty-submissions.print', $warrantySubmission) }}" class="btn btn-sm btn-secondary me-2" target="_blank">
                        <i class="fas fa-print me-2"></i>Print Memo
                    </a>
                    <a href="{{ route('warranty-submissions.edit', $warrantySubmission) }}" class="btn btn-sm btn-warning me-2">Update Status</a>
                    <a href="{{ route('warranty-submissions.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <div class="p-4">
                <!-- Status Badge -->
                <div class="text-center mb-4">
                    <span class="badge 
                        {{ $warrantySubmission->status === 'completed' ? 'bg-success fs-5' : 
                           ($warrantySubmission->status === 'returned' ? 'bg-info fs-5' : 
                           ($warrantySubmission->status === 'cancelled' ? 'bg-danger fs-5' : 
                           ($warrantySubmission->status === 'in_progress' ? 'bg-warning text-dark fs-5' : 'bg-secondary fs-5'))) }} px-4 py-2">
                        {{ ucfirst(str_replace('_', ' ', $warrantySubmission->status)) }}
                    </span>
                    @if($warrantySubmission->warranty)
                        @if($warrantySubmission->warranty->isActive())
                            <span class="badge bg-success fs-5 ms-2 px-4 py-2">Under Warranty</span>
                        @else
                            <span class="badge bg-danger fs-5 ms-2 px-4 py-2">Warranty Expired</span>
                        @endif
                    @endif
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Product & Warranty Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Memo Number:</th><td><strong>{{ $warrantySubmission->memo_number }}</strong></td></tr>
                            <tr><th>Barcode:</th><td><code>{{ $warrantySubmission->barcode ?? 'N/A' }}</code></td></tr>
                            <tr><th>Product:</th><td><strong>{{ $warrantySubmission->product->name }}</strong></td></tr>
                            @if($warrantySubmission->warranty)
                            <tr><th>Warranty Status:</th><td>@if($warrantySubmission->warranty->isActive())<span class="badge bg-success">Active</span>@else<span class="badge bg-danger">Expired</span>@endif</td></tr>
                            <tr><th>Warranty Ends:</th><td>{{ $warrantySubmission->warranty->end_date->format('d M Y') }}</td></tr>
                            <tr><th>Days Remaining:</th><td>{{ $warrantySubmission->warranty->daysRemaining() }} days</td></tr>
                            @endif
                            @if($warrantySubmission->sale)
                            <tr><th>Invoice Number:</th><td><strong>{{ $warrantySubmission->sale->invoice_number }}</strong></td></tr>
                            @endif
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Customer Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Name:</th><td><strong>{{ $warrantySubmission->customer_name }}</strong></td></tr>
                            <tr><th>Phone:</th><td>{{ $warrantySubmission->customer_phone }}</td></tr>
                            @if($warrantySubmission->customer_address)
                            <tr><th>Address:</th><td>{{ $warrantySubmission->customer_address }}</td></tr>
                            @endif
                            <tr><th>Submission Date:</th><td><strong>{{ $warrantySubmission->submission_date->format('d M Y') }}</strong></td></tr>
                            @if($warrantySubmission->expected_completion_date)
                            <tr><th>Expected Completion:</th><td>{{ $warrantySubmission->expected_completion_date->format('d M Y') }}</td></tr>
                            @endif
                            @if($warrantySubmission->completion_date)
                            <tr><th>Completed Date:</th><td>{{ $warrantySubmission->completion_date->format('d M Y') }}</td></tr>
                            @endif
                            @if($warrantySubmission->return_date)
                            <tr><th>Returned Date:</th><td>{{ $warrantySubmission->return_date->format('d M Y') }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-2 mb-3">Problem Details</h6>
                        <div class="mb-3">
                            <strong>Problem Description:</strong>
                            <p class="bg-light p-3 rounded">{{ $warrantySubmission->problem_description }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Customer Complaint:</strong>
                            <p class="bg-light p-3 rounded">{{ $warrantySubmission->customer_complaint }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Physical Condition:</strong>
                            <span class="badge bg-secondary">{{ ucfirst($warrantySubmission->condition) }}</span>
                            @if($warrantySubmission->physical_condition_notes)
                                <p class="mt-2 bg-light p-2 rounded small">{{ $warrantySubmission->physical_condition_notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($warrantySubmission->service_notes || $warrantySubmission->internal_notes || $warrantySubmission->service_charge)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-2 mb-3">Service Information</h6>
                        @if($warrantySubmission->service_notes)
                        <div class="mb-3">
                            <strong>Service Notes:</strong>
                            <p class="bg-light p-3 rounded">{{ $warrantySubmission->service_notes }}</p>
                        </div>
                        @endif
                        @if($warrantySubmission->service_charge)
                        <div class="mb-3">
                            <strong>Service Charge:</strong>
                            <span class="fs-5 text-primary">৳{{ number_format($warrantySubmission->service_charge, 2) }}</span>
                        </div>
                        @endif
                        @if($warrantySubmission->internal_notes)
                        <div class="mb-3">
                            <strong>Internal Notes:</strong>
                            <p class="bg-light p-3 rounded small">{{ $warrantySubmission->internal_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

