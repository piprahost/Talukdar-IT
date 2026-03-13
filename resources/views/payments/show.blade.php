@extends('layouts.dashboard')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-money-bill-wave me-2"></i>Payment: {{ $payment->payment_number }}</h6>
                <div>
                    <a href="{{ route('payments.edit', $payment) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Payment Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Payment Number:</th><td><strong>{{ $payment->payment_number }}</strong></td></tr>
                            <tr><th>Payment Type:</th><td><span class="badge {{ $payment->payment_type === 'customer' ? 'bg-success' : 'bg-primary' }}">{{ ucfirst($payment->payment_type) }}</span></td></tr>
                            <tr><th>Amount:</th><td><strong class="fs-5">৳{{ number_format($payment->amount, 2) }}</strong></td></tr>
                            <tr><th>Payment Date:</th><td>{{ $payment->payment_date->format('d M Y') }}</td></tr>
                            <tr><th>Payment Method:</th><td>{{ $payment->payment_method_name }}</td></tr>
                            @if($payment->reference_number)
                            <tr><th>Reference Number:</th><td><code>{{ $payment->reference_number }}</code></td></tr>
                            @endif
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">{{ $payment->payment_type === 'customer' ? 'Customer' : 'Supplier' }} Information</h6>
                        <table class="table table-borderless">
                            @if($payment->payment_type === 'customer')
                                @if($payment->sale)
                                <tr><th width="40%">Invoice:</th><td><a href="{{ route('sales.show', $payment->sale) }}">{{ $payment->sale->invoice_number }}</a></td></tr>
                                @endif
                                @if($payment->customer)
                                <tr><th>Customer:</th><td><strong>{{ $payment->customer->name }}</strong></td></tr>
                                <tr><th>Phone:</th><td>{{ $payment->customer->phone }}</td></tr>
                                @endif
                            @else
                                @if($payment->purchase)
                                <tr><th width="40%">PO Number:</th><td><a href="{{ route('purchases.show', $payment->purchase) }}">{{ $payment->purchase->po_number }}</a></td></tr>
                                @endif
                                @if($payment->supplier)
                                <tr><th>Supplier:</th><td><strong>{{ $payment->supplier->name }}</strong></td></tr>
                                <tr><th>Phone:</th><td>{{ $payment->supplier->phone }}</td></tr>
                                @endif
                            @endif
                            <tr><th>Created By:</th><td>{{ $payment->creator->name ?? 'N/A' }}</td></tr>
                            <tr><th>Created At:</th><td>{{ $payment->created_at->format('d M Y, h:i A') }}</td></tr>
                        </table>
                    </div>
                </div>
                
                @if($payment->notes)
                <div class="mt-4">
                    <h6>Notes</h6>
                    <p class="bg-light p-3 rounded">{{ $payment->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

