@extends('layouts.dashboard')

@section('title', 'Journal Entries')
@section('page-title', 'Journal Entries')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-book-open me-2"></i>All Journal Entries</h6>
        <a href="{{ route('journal-entries.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Entry
        </a>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3 mb-2">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search entry number, description...">
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-select" name="status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                    <option value="posted" {{ request('status')=='posted'?'selected':'' }}>Posted</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="From Date">
            </div>
            <div class="col-md-2 mb-2">
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="To Date">
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
                    <th>Entry #</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Reference</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                    <tr>
                        <td><strong>{{ $entry->entry_number }}</strong></td>
                        <td>{{ $entry->entry_date->format('d M Y') }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($entry->description, 50) }}</td>
                        <td>{{ $entry->reference ?? '-' }}</td>
                        <td><strong>৳{{ number_format($entry->total_debit, 2) }}</strong></td>
                        <td><strong>৳{{ number_format($entry->total_credit, 2) }}</strong></td>
                        <td>
                            <span class="badge {{ $entry->status === 'posted' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ucfirst($entry->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('journal-entries.show', $entry) }}" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($entry->status === 'draft')
                                    <a href="{{ route('journal-entries.edit', $entry) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No journal entries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $entries->links() }}
</div>
@endsection

