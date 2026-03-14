@extends('layouts.dashboard')

@section('title', 'General Ledger')
@section('page-title', 'General Ledger')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-file-alt me-2"></i>General Ledger</h6>
    </div>

    <div class="filter-wrapper">
    <form method="GET">
        <div class="row g-2 align-items-end">
            <div class="col-md-4 mb-3">
                <label for="account_id" class="form-label">Select Account <span class="text-danger">*</span></label>
                <select class="form-select" id="account_id" name="account_id" onchange="this.form.submit()" required>
                    <option value="">Select Account</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ request('account_id')==$account->id?'selected':'' }}>
                            {{ $account->code }} - {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-3 mb-3">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-2 mb-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Generate</button>
            </div>
        </div>
    </form>
    </div>

    @if($account)
    <div class="p-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h5>{{ $account->code }} - {{ $account->name }}</h5>
                <p class="text-muted">Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('accounting.ledger.export', ['format' => 'csv'] + request()->query()) }}"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                        <li><a class="dropdown-item" href="{{ route('accounting.ledger.export', ['format' => 'excel'] + request()->query()) }}"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                        <li><a class="dropdown-item" href="{{ route('accounting.ledger.export', ['format' => 'pdf'] + request()->query()) }}"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="alert alert-info">
                    <strong>Opening Balance:</strong><br>
                    ৳{{ number_format($openingBalance, 2) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-success">
                    <strong>Closing Balance:</strong><br>
                    ৳{{ number_format($closingBalance, 2) }}
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Entry #</th>
                        <th>Description</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $runningBalance = $openingBalance;
                    @endphp
                    <tr class="table-secondary">
                        <td colspan="5" class="text-end"><strong>Opening Balance</strong></td>
                        <td class="text-end"><strong>৳{{ number_format($openingBalance, 2) }}</strong></td>
                    </tr>
                    @forelse($entries as $item)
                        @php
                            if($account->balance_type === 'debit') {
                                $runningBalance += $item->debit - $item->credit;
                            } else {
                                $runningBalance += $item->credit - $item->debit;
                            }
                        @endphp
                        <tr>
                            <td>{{ $item->journalEntry->entry_date->format('d M Y') }}</td>
                            <td><a href="{{ route('journal-entries.show', $item->journalEntry) }}">{{ $item->journalEntry->entry_number }}</a></td>
                            <td>{{ $item->description ?? $item->journalEntry->description }}</td>
                            <td class="text-end">{{ $item->debit > 0 ? '৳' . number_format($item->debit, 2) : '-' }}</td>
                            <td class="text-end">{{ $item->credit > 0 ? '৳' . number_format($item->credit, 2) : '-' }}</td>
                            <td class="text-end"><strong>৳{{ number_format($runningBalance, 2) }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No transactions found for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

