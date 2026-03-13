@extends('layouts.dashboard')

@section('title', 'Sale Returns')
@section('page-title', 'Sale Returns')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-undo me-2"></i>All Sale Returns</h6>
        <a href="{{ route('sale-returns.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Return
        </a>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-2">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search return number, invoice, customer...">
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
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Return Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                    <tr>
                        <td><strong>{{ $return->return_number }}</strong></td>
                        <td>
                            <a href="{{ route('sales.show', $return->sale_id) }}">
                                {{ $return->sale->invoice_number }}
                            </a>
                        </td>
                        <td>{{ $return->sale->customer_name ?? ($return->customer ? $return->customer->name : 'Walk-in') }}</td>
                        <td>{{ $return->return_date->format('d M Y') }}</td>
                        <td><strong>৳{{ number_format($return->total_amount, 2) }}</strong></td>
                        <td>
                            <span class="badge 
                                {{ $return->status === 'completed' ? 'bg-success' : 
                                   ($return->status === 'cancelled' ? 'bg-danger' : 
                                   ($return->status === 'approved' ? 'bg-info' : 'bg-warning text-dark')) }}">
                                {{ ucfirst($return->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('sale-returns.show', $return) }}" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($return->status === 'pending')
                                    <a href="{{ route('sale-returns.edit', $return) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No sale returns found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $returns->links() }}
</div>
@endsection

