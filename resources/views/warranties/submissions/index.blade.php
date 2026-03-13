@extends('layouts.dashboard')

@section('title', 'Warranty Submissions')
@section('page-title', 'Warranty Submissions')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-file-alt me-2"></i>All Warranty Submissions</h6>
        <a href="{{ route('warranty-submissions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Submission
        </a>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-2">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search memo, barcode, customer...">
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-select" name="status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                    <option value="received" {{ request('status')=='received'?'selected':'' }}>Received</option>
                    <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
                    <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                    <option value="returned" {{ request('status')=='returned'?'selected':'' }}>Returned</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-select" name="warranty_status" onchange="this.form.submit()">
                    <option value="">All Warranty</option>
                    <option value="active" {{ request('warranty_status')=='active'?'selected':'' }}>Active Warranty</option>
                    <option value="expired" {{ request('warranty_status')=='expired'?'selected':'' }}>Expired Warranty</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-outline-primary w-100">Search</button>
            </div>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Memo #</th>
                    <th>Barcode</th>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Submission Date</th>
                    <th>Warranty Status</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $submission)
                    <tr>
                        <td><strong>{{ $submission->memo_number }}</strong></td>
                        <td><code>{{ $submission->barcode ?? 'N/A' }}</code></td>
                        <td>{{ $submission->product->name }}</td>
                        <td>{{ $submission->customer_name }}</td>
                        <td>{{ $submission->submission_date->format('d M Y') }}</td>
                        <td>
                            @if($submission->warranty && $submission->warranty->isActive())
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Expired</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge 
                                {{ $submission->status === 'completed' ? 'bg-success' : 
                                   ($submission->status === 'returned' ? 'bg-info' : 
                                   ($submission->status === 'cancelled' ? 'bg-danger' : 
                                   ($submission->status === 'in_progress' ? 'bg-warning text-dark' : 'bg-secondary'))) }}">
                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('warranty-submissions.show', $submission) }}" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('warranty-submissions.print', $submission) }}" class="btn btn-secondary" title="Print Memo" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="{{ route('warranty-submissions.edit', $submission) }}" class="btn btn-warning" title="Update Status">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No warranty submissions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $submissions->links() }}
</div>
@endsection

