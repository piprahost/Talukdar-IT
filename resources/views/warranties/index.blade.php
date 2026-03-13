@extends('layouts.dashboard')

@section('title', 'Warranties')
@section('page-title', 'Warranties')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-shield-alt me-2"></i>All Warranties</h6>
        <a href="{{ route('warranties.verify') }}" class="btn btn-primary">
            <i class="fas fa-search me-2"></i>Verify Warranty
        </a>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-2">
                <input type="text" class="form-control" name="barcode" value="{{ request('barcode') }}" placeholder="Search by barcode...">
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-select" name="status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                    <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Expired</option>
                    <option value="voided" {{ request('status')=='voided'?'selected':'' }}>Voided</option>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <button type="submit" class="btn btn-outline-primary w-100">Search</button>
            </div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Period</th>
                    <th>Status</th>
                    <th>Remaining</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warranties as $warranty)
                    <tr>
                        <td><code>{{ $warranty->barcode ?? 'N/A' }}</code></td>
                        <td><strong>{{ $warranty->product->name }}</strong></td>
                        <td>{{ $warranty->customer ? $warranty->customer->name : ($warranty->sale->customer_name ?? 'Walk-in') }}</td>
                        <td>{{ $warranty->start_date->format('d M Y') }}</td>
                        <td>{{ $warranty->end_date->format('d M Y') }}</td>
                        <td>{{ $warranty->warranty_period_days }} days</td>
                        <td>
                            @if($warranty->isActive())
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Expired</span>
                            @endif
                        </td>
                        <td>
                            @if($warranty->isActive())
                                <strong class="text-success">{{ $warranty->daysRemaining() }} days</strong>
                            @else
                                <span class="text-danger">Expired {{ $warranty->daysExpired() }} days ago</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('warranties.show', $warranty) }}" class="btn btn-sm btn-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center">No warranties found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $warranties->links() }}
</div>
@endsection

