@extends('layouts.dashboard')

@section('title', 'Journal Entries')
@section('page-title', 'Journal Entries')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-book-open me-2"></i>Journal Entries</h6>
        @can('create journal-entries')
        <a href="{{ route('journal-entries.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>New Entry
        </a>
        @endcan
    </div>

    <div class="filter-wrapper">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Entry #, description...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="draft"  {{ request('status')=='draft'  ?'selected':'' }}>Draft</option>
                        <option value="posted" {{ request('status')=='posted' ?'selected':'' }}>Posted</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}" placeholder="From">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}" placeholder="To">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary flex-grow-1">Search</button>
                    @if(request()->anyFilled(['search','status','date_from','date_to']))
                    <a href="{{ route('journal-entries.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Entry #</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Reference</th>
                    <th class="text-end">Total Debit</th>
                    <th class="text-end">Total Credit</th>
                    <th class="text-center">Balanced</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                @php $balanced = abs($entry->total_debit - $entry->total_credit) < 0.01; @endphp
                <tr>
                    <td><strong style="font-size:13px;">{{ $entry->entry_number }}</strong></td>
                    <td style="font-size:13px;">{{ $entry->entry_date->format('d M Y') }}</td>
                    <td style="max-width:200px;font-size:13px;">
                        {{ \Illuminate\Support\Str::limit($entry->description, 50) }}
                    </td>
                    <td style="font-size:12px;color:#9ca3af;">{{ $entry->reference ?? '—' }}</td>
                    <td class="text-end fw-semibold" style="color:#ef4444;">৳{{ number_format($entry->total_debit, 2) }}</td>
                    <td class="text-end fw-semibold" style="color:#16a34a;">৳{{ number_format($entry->total_credit, 2) }}</td>
                    <td class="text-center">
                        @if($balanced)
                            <span class="badge bg-success" style="font-size:10px;">✓</span>
                        @else
                            <span class="badge bg-danger" style="font-size:10px;">!</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($entry->status === 'posted')
                            <span style="background:#f0fdf4;color:#166534;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;">Posted</span>
                        @else
                            <span style="background:#fff7ed;color:#c2410c;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;">Draft</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('journal-entries.show', $entry) }}" class="btn btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                            @if($entry->status === 'draft')
                            <a href="{{ route('journal-entries.edit', $entry) }}" class="btn btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="fas fa-book-open fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">No journal entries found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($entries->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
        <small class="text-muted">Showing {{ $entries->firstItem() }}–{{ $entries->lastItem() }} of {{ $entries->total() }} entries</small>
        {{ $entries->links() }}
    </div>
    @endif
</div>
@endsection
