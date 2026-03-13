@extends('layouts.dashboard')

@section('title', 'Warranties')
@section('page-title', 'Warranty Management')

@section('content')

{{-- Stats --}}
<div class="module-stats mb-3">
    <div class="module-stat-card">
        <div class="msc-label">Total Warranties</div>
        <div class="msc-value">{{ $stats['total'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #16a34a;">
        <div class="msc-label">Active</div>
        <div class="msc-value" style="color:#16a34a;">{{ $stats['active'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #ef4444;">
        <div class="msc-label">Expired</div>
        <div class="msc-value" style="color:#ef4444;">{{ $stats['expired'] }}</div>
    </div>
    <div class="module-stat-card" style="border-left:3px solid #f97316;">
        <div class="msc-label">Expiring Soon</div>
        <div class="msc-value" style="color:#f97316;">{{ $stats['expiring'] }}</div>
        <div class="msc-sub">Within 30 days</div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-shield-alt me-2"></i>All Warranties</h6>
        <div class="d-flex gap-2">
            @can('verify warranties')
            <a href="{{ route('warranties.verify') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-search me-1"></i>Verify Warranty
            </a>
            @endcan
            @can('view warranty-submissions')
            <a href="{{ route('warranty-submissions.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i>New Submission
            </a>
            @endcan
        </div>
    </div>

    <div class="filter-wrapper">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                        <input type="text" class="form-control" name="barcode" value="{{ request('barcode') }}" placeholder="Search by barcode...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active"  {{ request('status')=='active'  ?'selected':'' }}>Active</option>
                        <option value="expired" {{ request('status')=='expired' ?'selected':'' }}>Expired</option>
                        <option value="voided"  {{ request('status')=='voided'  ?'selected':'' }}>Voided</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary flex-grow-1">Search</button>
                    @if(request()->anyFilled(['barcode','status']))
                    <a href="{{ route('warranties.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Start Date</th>
                    <th>Expiry Date</th>
                    <th class="text-center">Period</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Remaining</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warranties as $warranty)
                @php
                    $isActive = $warranty->isActive();
                    $daysLeft = $isActive ? $warranty->daysRemaining() : null;
                    $isExpiringSoon = $isActive && $daysLeft <= 30;
                @endphp
                <tr class="{{ $isExpiringSoon ? 'table-warning' : '' }}">
                    <td>
                        <code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:12px;">
                            {{ $warranty->barcode ?? 'N/A' }}
                        </code>
                    </td>
                    <td class="fw-semibold" style="font-size:14px;">{{ $warranty->product->name }}</td>
                    <td style="font-size:13px;">
                        {{ $warranty->customer ? $warranty->customer->name : ($warranty->sale->customer_name ?? 'Walk-in') }}
                    </td>
                    <td style="font-size:13px;">{{ $warranty->start_date->format('d M Y') }}</td>
                    <td style="font-size:13px;">
                        {{ $warranty->end_date->format('d M Y') }}
                        @if($isExpiringSoon)
                            <div style="font-size:10px;color:#d97706;font-weight:600;">⚠ Expiring soon</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background:#eff6ff;color:#1d4ed8;">{{ $warranty->warranty_period_days }}d</span>
                    </td>
                    <td class="text-center">
                        @if($isActive)
                            <span class="badge bg-success" style="font-size:11px;">Active</span>
                        @elseif($warranty->status === 'voided')
                            <span class="badge bg-secondary" style="font-size:11px;">Voided</span>
                        @else
                            <span class="badge bg-danger" style="font-size:11px;">Expired</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($isActive)
                            <span class="fw-semibold" style="font-size:13px;color:#16a34a;">{{ $daysLeft }}d</span>
                        @else
                            <span style="font-size:12px;color:#ef4444;">
                                {{ $warranty->daysExpired() }}d ago
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('warranties.show', $warranty) }}" class="btn btn-outline-primary" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('view warranty-submissions')
                            <a href="{{ route('warranty-submissions.create', ['barcode' => $warranty->barcode]) }}" class="btn btn-outline-success" title="Create Submission">
                                <i class="fas fa-file-alt"></i>
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="fas fa-shield-alt fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">No warranties found. Warranties are created automatically when sales are completed.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($warranties->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">Showing {{ $warranties->firstItem() }}–{{ $warranties->lastItem() }} of {{ $warranties->total() }} warranties</small>
        {{ $warranties->links() }}
    </div>
    @endif
</div>
@endsection
