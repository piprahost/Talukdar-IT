@extends('layouts.dashboard')

@section('title', 'Journal Entry Details')
@section('page-title', 'Journal Entry Details')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-book-open me-2"></i>Entry: {{ $journalEntry->entry_number }}</h6>
                <div>
                    @if($journalEntry->status === 'draft')
                        <form action="{{ route('journal-entries.post', $journalEntry) }}" method="POST" class="d-inline me-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Post this journal entry?');">
                                <i class="fas fa-check me-2"></i>Post Entry
                            </button>
                        </form>
                        <a href="{{ route('journal-entries.edit', $journalEntry) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                    @else
                        <form action="{{ route('journal-entries.unpost', $journalEntry) }}" method="POST" class="d-inline me-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Unpost this journal entry?');">
                                <i class="fas fa-undo me-2"></i>Unpost Entry
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('journal-entries.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            
            <div class="p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Entry Information</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Entry Number:</th><td><strong>{{ $journalEntry->entry_number }}</strong></td></tr>
                            <tr><th>Entry Date:</th><td>{{ $journalEntry->entry_date->format('d M Y') }}</td></tr>
                            <tr><th>Description:</th><td>{{ $journalEntry->description }}</td></tr>
                            <tr><th>Reference:</th><td>{{ $journalEntry->reference ?? '-' }}</td></tr>
                            <tr><th>Status:</th><td>
                                <span class="badge {{ $journalEntry->status === 'posted' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ ucfirst($journalEntry->status) }}
                                </span>
                            </td></tr>
                            @if($journalEntry->posted_at)
                            <tr><th>Posted At:</th><td>{{ $journalEntry->posted_at->format('d M Y, h:i A') }} by {{ $journalEntry->poster->name ?? 'N/A' }}</td></tr>
                            @endif
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Totals</h6>
                        <table class="table table-borderless">
                            <tr><th width="40%">Total Debit:</th><td><strong>৳{{ number_format($journalEntry->total_debit, 2) }}</strong></td></tr>
                            <tr><th>Total Credit:</th><td><strong>৳{{ number_format($journalEntry->total_credit, 2) }}</strong></td></tr>
                            <tr><th>Balance:</th><td>
                                <span class="badge {{ abs($journalEntry->total_debit - $journalEntry->total_credit) < 0.01 ? 'bg-success' : 'bg-danger' }}">
                                    {{ abs($journalEntry->total_debit - $journalEntry->total_credit) < 0.01 ? 'Balanced' : 'Not Balanced' }}
                                </span>
                            </td></tr>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6 class="border-bottom pb-2 mb-3">Entry Items</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th class="text-end">Debit</th>
                                    <th class="text-end">Credit</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($journalEntry->items as $item)
                                    <tr>
                                        <td><strong>{{ $item->account->code }}</strong> - {{ $item->account->name }}</td>
                                        <td class="text-end">{{ $item->debit > 0 ? '৳' . number_format($item->debit, 2) : '-' }}</td>
                                        <td class="text-end">{{ $item->credit > 0 ? '৳' . number_format($item->credit, 2) : '-' }}</td>
                                        <td>{{ $item->description ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-secondary">
                                    <th>Total:</th>
                                    <th class="text-end">৳{{ number_format($journalEntry->total_debit, 2) }}</th>
                                    <th class="text-end">৳{{ number_format($journalEntry->total_credit, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

