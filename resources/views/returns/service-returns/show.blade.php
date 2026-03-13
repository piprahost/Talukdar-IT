@extends('layouts.dashboard')

@section('title', 'Service Return Details')
@section('page-title', 'Service Return Details')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-undo me-2"></i>Return: {{ $serviceReturn->return_number }}</h6>
                <div>
                    @if($serviceReturn->status === 'pending')
                        <form action="{{ route('service-returns.approve', $serviceReturn) }}" method="POST" class="d-inline me-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this return?');">
                                <i class="fas fa-check me-2"></i>Approve
                            </button>
                        </form>
                        <a href="{{ route('service-returns.edit', $serviceReturn) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                    @endif
                    @if($serviceReturn->status === 'approved')
                        <form action="{{ route('service-returns.complete', $serviceReturn) }}" method="POST" class="d-inline me-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Complete this return?');">
                                <i class="fas fa-check-double me-2"></i>Complete Return
                            </button>
                        </form>
                    @endif
                    @if($serviceReturn->status === 'completed' && $serviceReturn->refund_status === 'pending')
                        <form action="{{ route('service-returns.process-refund', $serviceReturn) }}" method="POST" class="d-inline me-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Process refund of ৳{{ number_format($serviceReturn->refund_amount, 2) }}?');">
                                <i class="fas fa-money-bill-wave me-2"></i>Process Refund
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('service-returns.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Return Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Return Number:</th><td><strong>{{ $serviceReturn->return_number }}</strong></td></tr>
                            <tr><th>Service Number:</th><td><a href="{{ route('services.show', $serviceReturn->service_id) }}">{{ $serviceReturn->service->service_number }}</a></td></tr>
                            <tr><th>Customer:</th><td>{{ $serviceReturn->service->customer_name }}</td></tr>
                            <tr><th>Product:</th><td>{{ $serviceReturn->service->product_name }}</td></tr>
                            <tr><th>Return Date:</th><td>{{ $serviceReturn->return_date->format('d M Y') }}</td></tr>
                            <tr><th>Status:</th><td>
                                <span class="badge 
                                    {{ $serviceReturn->status === 'completed' ? 'bg-success' : 
                                       ($serviceReturn->status === 'cancelled' ? 'bg-danger' : 
                                       ($serviceReturn->status === 'approved' ? 'bg-info' : 'bg-warning text-dark')) }}">
                                    {{ ucfirst($serviceReturn->status) }}
                                </span>
                            </td></tr>
                            <tr><th>Refund Status:</th><td>
                                <span class="badge {{ $serviceReturn->refund_status === 'processed' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ ucfirst($serviceReturn->refund_status) }}
                                </span>
                            </td></tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Refund Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Service Cost:</th><td>৳{{ number_format($serviceReturn->service->service_cost, 2) }}</td></tr>
                            <tr><th>Refund Amount:</th><td><strong class="fs-5">৳{{ number_format($serviceReturn->refund_amount, 2) }}</strong></td></tr>
                            <tr><th>Created By:</th><td>{{ $serviceReturn->creator->name ?? 'N/A' }}</td></tr>
                            @if($serviceReturn->approver)
                            <tr><th>Approved By:</th><td>{{ $serviceReturn->approver->name }}<br><small class="text-muted">{{ $serviceReturn->approved_at->format('d M Y, h:i A') }}</small></td></tr>
                            @endif
                            <tr><th>Created At:</th><td>{{ $serviceReturn->created_at->format('d M Y, h:i A') }}</td></tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Return Reason</h6>
                        <p class="bg-light p-3 rounded">{{ $serviceReturn->reason }}</p>
                    </div>
                </div>
                
                @if($serviceReturn->notes)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Notes</h6>
                        <p class="bg-light p-3 rounded">{{ $serviceReturn->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

