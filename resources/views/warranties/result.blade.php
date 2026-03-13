@extends('layouts.dashboard')

@section('title', 'Warranty Verification Result')
@section('page-title', 'Warranty Check Result')

@section('content')
@php
    $isActive  = $warranty->isActive();
    $daysLeft  = $isActive ? $warranty->daysRemaining() : 0;
    $daysUsed  = $warranty->warranty_period_days - $daysLeft;
    $progress  = $warranty->getProgressPercentage();
    $isExpSoon = $isActive && $daysLeft <= 30;
@endphp

<div class="row g-3 justify-content-center">
    <div class="col-md-8">

        {{-- Big Status Banner --}}
        <div class="rounded-3 p-4 mb-3 text-center" style="background:{{ $isActive ? 'linear-gradient(135deg,#f0fdf4,#dcfce7)' : 'linear-gradient(135deg,#fef2f2,#fee2e2)' }};border:2px solid {{ $isActive ? '#16a34a' : '#ef4444' }};">
            <div style="width:72px;height:72px;border-radius:50%;background:{{ $isActive ? '#16a34a' : '#ef4444' }};display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 8px 24px {{ $isActive ? 'rgba(22,163,74,0.3)' : 'rgba(239,68,68,0.3)' }};">
                <i class="fas {{ $isActive ? 'fa-shield-alt' : 'fa-shield-xmark' }}" style="color:white;font-size:32px;"></i>
            </div>
            <h2 style="font-size:28px;font-weight:800;color:{{ $isActive ? '#166534' : '#991b1b' }};margin-bottom:8px;">
                {{ $isActive ? '✓ WARRANTY ACTIVE' : '✗ WARRANTY EXPIRED' }}
            </h2>
            @if($isActive)
                @if($isExpSoon)
                    <p style="color:#d97706;font-weight:600;margin-bottom:4px;">⚠ Expiring in {{ $daysLeft }} days ({{ $warranty->end_date->format('d M Y') }})</p>
                @else
                    <p style="color:#166534;font-size:18px;font-weight:700;margin-bottom:4px;">{{ $daysLeft }} days remaining</p>
                @endif
                <p style="color:#6b7280;font-size:14px;margin:0;">Valid until {{ $warranty->end_date->format('d M Y') }}</p>
            @else
                <p style="color:#991b1b;font-size:16px;font-weight:600;margin-bottom:4px;">Expired {{ $warranty->daysExpired() }} days ago</p>
                <p style="color:#6b7280;font-size:14px;margin:0;">Expired on {{ $warranty->end_date->format('d M Y') }}</p>
            @endif
        </div>

        {{-- Warranty Progress Bar --}}
        <div class="table-card mb-3 p-4">
            <h6 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#9ca3af;margin-bottom:12px;">Warranty Timeline</h6>
            <div style="position:relative;height:12px;background:#e5e7eb;border-radius:6px;overflow:hidden;margin-bottom:8px;">
                <div style="position:absolute;left:0;top:0;height:100%;width:{{ min($progress, 100) }}%;background:{{ $isActive ? ($isExpSoon ? '#f97316' : '#16a34a') : '#ef4444' }};border-radius:6px;transition:width .8s ease;"></div>
            </div>
            <div class="d-flex justify-content-between" style="font-size:12px;color:#6b7280;">
                <span>Start: {{ $warranty->start_date->format('d M Y') }}</span>
                <span style="font-weight:700;color:{{ $isActive ? '#16a34a' : '#ef4444' }};">{{ number_format($progress, 0) }}% used</span>
                <span>End: {{ $warranty->end_date->format('d M Y') }}</span>
            </div>
            <div class="d-flex justify-content-between mt-2" style="font-size:12px;">
                <span class="text-muted">{{ $warranty->warranty_period_days }} days total</span>
                <span class="text-muted">{{ $daysUsed }} days used · {{ $daysLeft }} days left</span>
            </div>
        </div>

        {{-- Product & Customer Details --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="table-card h-100">
                    <div class="table-card-header">
                        <h6><i class="fas fa-box me-2"></i>Product Details</h6>
                    </div>
                    <div class="p-3">
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr>
                                <td class="text-muted ps-0" style="width:40%;">Barcode</td>
                                <td class="pe-0"><code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:12px;">{{ $warranty->barcode ?? 'N/A' }}</code></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Product</td>
                                <td class="pe-0 fw-semibold">{{ $warranty->product->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">SKU</td>
                                <td class="pe-0"><code style="font-size:12px;">{{ $warranty->product->sku }}</code></td>
                            </tr>
                            @if($warranty->sale)
                            <tr>
                                <td class="text-muted ps-0">Invoice</td>
                                <td class="pe-0">
                                    <a href="{{ route('sales.show', $warranty->sale) }}" class="text-primary">{{ $warranty->sale->invoice_number }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Sale Date</td>
                                <td class="pe-0">{{ $warranty->sale->sale_date->format('d M Y') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="table-card h-100">
                    <div class="table-card-header">
                        <h6><i class="fas fa-user me-2"></i>Customer Details</h6>
                    </div>
                    <div class="p-3">
                        <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                            <tr>
                                <td class="text-muted ps-0" style="width:40%;">Name</td>
                                <td class="pe-0 fw-semibold">
                                    {{ $warranty->customer ? $warranty->customer->name : ($warranty->sale->customer_name ?? 'Walk-in Customer') }}
                                </td>
                            </tr>
                            @if($warranty->customer && $warranty->customer->phone)
                            <tr>
                                <td class="text-muted ps-0">Phone</td>
                                <td class="pe-0"><a href="tel:{{ $warranty->customer->phone }}" style="color:inherit;">{{ $warranty->customer->phone }}</a></td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted ps-0">Warranty Period</td>
                                <td class="pe-0 fw-semibold">{{ $warranty->warranty_period_days }} days <small class="text-muted">(≈{{ number_format($warranty->warranty_period_days/30,1) }} mo)</small></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Start Date</td>
                                <td class="pe-0">{{ $warranty->start_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Expiry Date</td>
                                <td class="pe-0 {{ !$isActive ? 'text-danger fw-semibold' : '' }}">{{ $warranty->end_date->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            @if($isActive)
                @can('view warranty-submissions')
                <a href="{{ route('warranty-submissions.create', ['barcode' => $warranty->barcode]) }}" class="btn btn-success btn-lg">
                    <i class="fas fa-file-alt me-2"></i>Create Warranty Submission
                </a>
                @endcan
            @endif
            <a href="{{ route('warranties.verify') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-search me-2"></i>Verify Another
            </a>
            <a href="{{ route('warranties.show', $warranty) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-1"></i>Full Details
            </a>
        </div>
    </div>
</div>
@endsection
