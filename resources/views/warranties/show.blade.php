@extends('layouts.dashboard')

@section('title', 'Warranty Details')
@section('page-title', 'Warranty Details')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-shield-alt me-2"></i>Warranty Details</h6>
                <div>
                    <a href="{{ route('warranties.verify') }}" class="btn btn-sm btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Verify Another
                    </a>
                    <a href="{{ route('warranties.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <div class="p-4">
                <!-- Status Badge -->
                <div class="text-center mb-4">
                    @if($warranty->isActive())
                        <div class="alert alert-success">
                            <h4 class="mb-2"><i class="fas fa-check-circle me-2"></i>Warranty Active</h4>
                            <p class="mb-0">Days Remaining: <strong>{{ $warranty->daysRemaining() }} days</strong></p>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <h4 class="mb-2"><i class="fas fa-times-circle me-2"></i>Warranty Expired</h4>
                            <p class="mb-0">Expired {{ $warranty->daysExpired() }} days ago</p>
                        </div>
                    @endif
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Product Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Barcode:</th><td><code class="fs-5">{{ $warranty->barcode ?? 'N/A' }}</code></td></tr>
                            <tr><th>Product Name:</th><td><strong>{{ $warranty->product->name }}</strong></td></tr>
                            <tr><th>SKU:</th><td>{{ $warranty->product->sku }}</td></tr>
                            <tr><th>Category:</th><td>{{ $warranty->product->category->name ?? 'N/A' }}</td></tr>
                            <tr><th>Brand:</th><td>{{ $warranty->product->brand->name ?? 'N/A' }}</td></tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Customer & Sale Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Customer:</th><td>{{ $warranty->customer ? $warranty->customer->name : ($warranty->sale->customer_name ?? 'Walk-in Customer') }}</td></tr>
                            @if($warranty->sale)
                            <tr><th>Invoice Number:</th><td><strong>{{ $warranty->sale->invoice_number }}</strong></td></tr>
                            <tr><th>Sale Date:</th><td>{{ $warranty->sale->sale_date->format('d M Y') }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-2 mb-3">Warranty Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="20%">Status:</th><td>@if($warranty->isActive())<span class="badge bg-success fs-6">Active</span>@else<span class="badge bg-danger fs-6">Expired</span>@endif</td></tr>
                            <tr><th>Start Date:</th><td><strong>{{ $warranty->start_date->format('d M Y') }}</strong></td></tr>
                            <tr><th>End Date:</th><td><strong>{{ $warranty->end_date->format('d M Y') }}</strong></td></tr>
                            <tr><th>Warranty Period:</th><td><strong>{{ $warranty->warranty_period_days }} days</strong> ({{ number_format($warranty->warranty_period_days / 30, 1) }} months)</td></tr>
                            @if($warranty->isActive())
                            <tr><th>Days Remaining:</th><td><strong class="text-success">{{ $warranty->daysRemaining() }} days</strong></td></tr>
                            @else
                            <tr><th>Days Expired:</th><td><strong class="text-danger">{{ $warranty->daysExpired() }} days ago</strong></td></tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-4">
                    <h6>Warranty Progress</h6>
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar {{ $warranty->isActive() ? 'bg-success' : 'bg-danger' }}" 
                             role="progressbar" 
                             style="width: {{ $warranty->getProgressPercentage() }}%"
                             aria-valuenow="{{ $warranty->getProgressPercentage() }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ number_format($warranty->getProgressPercentage(), 1) }}% Complete
                        </div>
                    </div>
                    <small class="text-muted">
                        Started: {{ $warranty->start_date->format('d M Y') }} | 
                        Ends: {{ $warranty->end_date->format('d M Y') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

