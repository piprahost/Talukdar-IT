@extends('layouts.dashboard')

@section('title', 'Sale Returns')
@section('page-title', 'Sale Returns')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-undo me-2"></i>Sale Returns</h6>
        @can('create sale-returns')
        <a href="{{ route('sale-returns.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>New Return
        </a>
        @endcan
    </div>

    <div class="filter-wrapper">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Return #, invoice number, customer...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending"   {{ request('status')=='pending'   ?'selected':'' }}>Pending</option>
                        <option value="approved"  {{ request('status')=='approved'  ?'selected':'' }}>Approved</option>
                        <option value="completed" {{ request('status')=='completed' ?'selected':'' }}>Completed</option>
                        <option value="cancelled" {{ request('status')=='cancelled' ?'selected':'' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary flex-grow-1">Search</button>
                    @if(request()->anyFilled(['search','status']))
                    <a href="{{ route('sale-returns.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Return #</th>
                    <th>Original Invoice</th>
                    <th>Customer</th>
                    <th>Return Date</th>
                    <th class="text-end">Return Amount</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                @php
                    $statusColors = [
                        'pending'   => ['bg'=>'#fff7ed','color'=>'#c2410c'],
                        'approved'  => ['bg'=>'#eff6ff','color'=>'#1d4ed8'],
                        'completed' => ['bg'=>'#f0fdf4','color'=>'#166534'],
                        'cancelled' => ['bg'=>'#fef2f2','color'=>'#991b1b'],
                    ][$return->status] ?? ['bg'=>'#f3f4f6','color'=>'#374151'];
                @endphp
                <tr>
                    <td><strong style="font-size:13px;">{{ $return->return_number }}</strong></td>
                    <td>
                        <a href="{{ route('sales.show', $return->sale_id) }}" class="text-primary fw-semibold" style="font-size:13px;">
                            {{ $return->sale->invoice_number }}
                        </a>
                    </td>
                    <td style="font-size:13px;">
                        {{ $return->sale->customer_name ?? ($return->customer?->name ?? 'Walk-in') }}
                    </td>
                    <td style="font-size:13px;">{{ $return->return_date->format('d M Y') }}</td>
                    <td class="text-end fw-bold">৳{{ number_format($return->getDisplayTotalAmount(), 2) }}</td>
                    <td class="text-center">
                        <span style="background:{{ $statusColors['bg'] }};color:{{ $statusColors['color'] }};padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;">
                            {{ ucfirst($return->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('sale-returns.show', $return) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                            @if($return->status === 'pending')
                            <a href="{{ route('sale-returns.edit', $return) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-undo fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">No sale returns found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($returns->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">Showing {{ $returns->firstItem() }}–{{ $returns->lastItem() }} of {{ $returns->total() }} returns</small>
        {{ $returns->links() }}
    </div>
    @endif
</div>
@endsection
