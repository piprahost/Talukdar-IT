@extends('layouts.dashboard')

@section('title', 'Service Returns')
@section('page-title', 'Service Returns')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-undo me-2"></i>All Service Returns</h6>
        <a href="{{ route('service-returns.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Return
        </a>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-2">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search return number, service number, customer...">
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-select" name="status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                    <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                    <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                    <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
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
                    <th>Return #</th>
                    <th>Service #</th>
                    <th>Customer</th>
                    <th>Return Date</th>
                    <th>Refund Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                    <tr>
                        <td><strong>{{ $return->return_number }}</strong></td>
                        <td>
                            <a href="{{ route('services.show', $return->service_id) }}">
                                {{ $return->service->service_number }}
                            </a>
                        </td>
                        <td>{{ $return->service->customer_name }}</td>
                        <td>{{ $return->return_date->format('d M Y') }}</td>
                        <td><strong>৳{{ number_format($return->refund_amount, 2) }}</strong></td>
                        <td>
                            <span class="badge 
                                {{ $return->status === 'completed' ? 'bg-success' : 
                                   ($return->status === 'cancelled' ? 'bg-danger' : 
                                   ($return->status === 'approved' ? 'bg-info' : 'bg-warning text-dark')) }}">
                                {{ ucfirst($return->status) }}
                            </span>
                            @if($return->refund_status === 'processed')
                                <span class="badge bg-success ms-1">Refunded</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('service-returns.show', $return) }}" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($return->status === 'pending')
                                    <a href="{{ route('service-returns.edit', $return) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No service returns found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $returns->links() }}
</div>
@endsection

