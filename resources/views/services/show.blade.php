@extends('layouts.dashboard')

@section('title', 'Service Details')
@section('page-title', 'Service Order Details')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-laptop-medical me-2"></i>Service Order: #{{ $service->service_number }}</h6>
                <div>
                    @if(in_array($service->status, ['completed', 'delivered']))
                        <a href="{{ route('service-returns.create', ['service_id' => $service->id]) }}" class="btn btn-sm btn-danger me-2">
                            <i class="fas fa-undo me-2"></i>Return Service
                        </a>
                    @endif
                    @if($service->returns && $service->returns->count() > 0)
                        <a href="{{ route('service-returns.index', ['search' => $service->service_number]) }}" class="btn btn-sm btn-info me-2">
                            <i class="fas fa-list me-2"></i>View Returns ({{ $service->returns->count() }})
                        </a>
                    @endif
                    <a href="{{ route('services.print', $service) }}" class="btn btn-sm btn-secondary me-2" target="_blank">
                        <i class="fas fa-print me-2"></i>Print Memo
                    </a>
                    <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-warning me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-box me-2"></i>Product Information</h6>
                    <div class="mb-3">
                        <strong>Product Name:</strong><br>
                        <span>{{ $service->product_name }}</span>
                    </div>
                    @if($service->serial_number)
                    <div class="mb-3">
                        <strong>Serial Number:</strong><br>
                        <span>{{ $service->serial_number }}</span>
                    </div>
                    @endif
                    @if($service->problem_notes)
                    <div class="mb-3">
                        <strong>Problem Description:</strong><br>
                        <span>{{ $service->problem_notes }}</span>
                    </div>
                    @endif
                    @if($service->service_notes)
                    <div class="mb-3">
                        <strong>Service Notes:</strong><br>
                        <span>{{ $service->service_notes }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-user me-2"></i>Customer Information</h6>
                    <div class="mb-3">
                        <strong>Customer Name:</strong><br>
                        <span>{{ $service->customer_name }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Phone:</strong><br>
                        <span>{{ $service->customer_phone }}</span>
                    </div>
                    @if($service->customer_address)
                    <div class="mb-3">
                        <strong>Address:</strong><br>
                        <span>{{ $service->customer_address }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <hr>
            
            <div class="row">
                <div class="col-md-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-calendar me-2"></i>Dates</h6>
                    <div class="mb-3">
                        <strong>Receive Date:</strong><br>
                        <span>{{ $service->receive_date->format('F d, Y') }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Delivery Date:</strong><br>
                        <span>{{ $service->delivery_date ? $service->delivery_date->format('F d, Y') : 'Not set' }}</span>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-money-bill-wave me-2"></i>Payment (BDT)</h6>
                    <div class="mb-3">
                        <strong>Service Cost:</strong><br>
                        <span class="h5">৳{{ number_format($service->service_cost, 2) }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Paid Amount:</strong><br>
                        <span class="text-success">৳{{ number_format($service->paid_amount, 2) }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Due Amount:</strong><br>
                        <span class="text-danger">৳{{ number_format($service->due_amount, 2) }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Payment Method:</strong><br>
                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $service->payment_method ?? 'cash')) }}</span>
                    </div>
                    @if($service->bankAccount)
                    <div class="mb-3">
                        <strong>Bank Account:</strong><br>
                        <span>{{ $service->bankAccount->account_name }} - {{ $service->bankAccount->bank_name }}</span>
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Payment Status:</strong><br>
                        @if($service->due_amount == 0)
                            <span class="badge bg-success">Fully Paid</span>
                        @elseif($service->paid_amount == 0)
                            <span class="badge bg-danger">Unpaid</span>
                        @else
                            <span class="badge bg-warning">Partial Payment</span>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i>Status</h6>
                    @php
                        $statusClasses = [
                            'pending' => 'bg-secondary',
                            'in_progress' => 'bg-info',
                            'completed' => 'bg-success',
                            'delivered' => 'bg-primary',
                            'cancelled' => 'bg-danger'
                        ];
                        $statusLabels = [
                            'pending' => 'Pending',
                            'in_progress' => 'In Progress',
                            'completed' => 'Completed',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled'
                        ];
                    @endphp
                    <div class="mb-3">
                        <span class="badge {{ $statusClasses[$service->status] ?? 'bg-secondary' }} p-2" style="font-size: 14px;">
                            {{ $statusLabels[$service->status] ?? ucfirst($service->status) }}
                        </span>
                    </div>
                    @if($service->created_by)
                    <div class="mb-3">
                        <strong>Created By:</strong><br>
                        <small>{{ $service->creator->name ?? 'N/A' }}</small>
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        <small>{{ $service->created_at->format('F d, Y h:i A') }}</small>
                    </div>
                </div>
            </div>
            
            @if($service->internal_notes)
            <hr>
            <div class="mb-3">
                <h6><i class="fas fa-sticky-note me-2"></i>Internal Notes</h6>
                <p class="text-muted">{{ $service->internal_notes }}</p>
            </div>
            @endif
            
            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this service order?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete
                    </button>
                </form>
                <a href="{{ route('services.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

