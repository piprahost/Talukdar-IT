@extends('layouts.dashboard')

@section('title', 'Warranty Submissions')
@section('page-title', 'Warranty Submissions')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-file-alt me-2"></i>Warranty Submissions</h6>
        @can('create warranty-submissions')
        <a href="{{ route('warranty-submissions.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>New Submission
        </a>
        @endcan
    </div>

    <div class="filter-wrapper">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Memo #, barcode, customer...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending"     {{ request('status')=='pending'     ?'selected':'' }}>Pending</option>
                        <option value="received"    {{ request('status')=='received'    ?'selected':'' }}>Received</option>
                        <option value="in_progress" {{ request('status')=='in_progress' ?'selected':'' }}>In Progress</option>
                        <option value="completed"   {{ request('status')=='completed'   ?'selected':'' }}>Completed</option>
                        <option value="returned"    {{ request('status')=='returned'    ?'selected':'' }}>Returned</option>
                        <option value="cancelled"   {{ request('status')=='cancelled'   ?'selected':'' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="warranty_status" onchange="this.form.submit()">
                        <option value="">Any Warranty</option>
                        <option value="active"  {{ request('warranty_status')=='active'  ?'selected':'' }}>Active Warranty</option>
                        <option value="expired" {{ request('warranty_status')=='expired' ?'selected':'' }}>Expired Warranty</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary flex-grow-1">Search</button>
                    @if(request()->anyFilled(['search','status','warranty_status']))
                    <a href="{{ route('warranty-submissions.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Memo #</th>
                    <th>Product / Barcode</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th class="text-center">Warranty</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $submission)
                @php
                    $warrantyActive = $submission->warranty && $submission->warranty->isActive();
                    $statusColors = [
                        'pending'     => ['bg'=>'#fff7ed','color'=>'#c2410c'],
                        'received'    => ['bg'=>'#eff6ff','color'=>'#1d4ed8'],
                        'in_progress' => ['bg'=>'#fefce8','color'=>'#854d0e'],
                        'completed'   => ['bg'=>'#f0fdf4','color'=>'#166534'],
                        'returned'    => ['bg'=>'#f0fdf4','color'=>'#166534'],
                        'cancelled'   => ['bg'=>'#fef2f2','color'=>'#991b1b'],
                    ][$submission->status] ?? ['bg'=>'#f3f4f6','color'=>'#374151'];
                @endphp
                <tr>
                    <td>
                        <strong style="font-size:13px;">{{ $submission->memo_number }}</strong>
                    </td>
                    <td>
                        <div class="fw-semibold" style="font-size:14px;">{{ $submission->product->name }}</div>
                        @if($submission->barcode)
                        <code style="background:#f3f4f6;padding:1px 5px;border-radius:4px;font-size:11px;">{{ $submission->barcode }}</code>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $submission->customer_name }}</td>
                    <td style="font-size:13px;">{{ $submission->submission_date->format('d M Y') }}</td>
                    <td class="text-center">
                        @if($warrantyActive)
                            <span class="badge bg-success" style="font-size:11px;">✓ Active</span>
                        @else
                            <span class="badge bg-danger" style="font-size:11px;">Expired</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span style="background:{{ $statusColors['bg'] }};color:{{ $statusColors['color'] }};padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;">
                            {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('warranty-submissions.show', $submission) }}" class="btn btn-outline-primary" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('warranty-submissions.print', $submission) }}" class="btn btn-outline-secondary" title="Print" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                            @if(!in_array($submission->status, ['completed','cancelled']))
                            <a href="{{ route('warranty-submissions.edit', $submission) }}" class="btn btn-outline-warning" title="Update">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">No warranty submissions found.</p>
                        @can('create warranty-submissions')
                        <a href="{{ route('warranty-submissions.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Create Submission
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($submissions->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">Showing {{ $submissions->firstItem() }}–{{ $submissions->lastItem() }} of {{ $submissions->total() }} submissions</small>
        {{ $submissions->links() }}
    </div>
    @endif
</div>
@endsection
