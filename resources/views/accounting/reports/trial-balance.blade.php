@extends('layouts.dashboard')

@section('title', 'Trial Balance')
@section('page-title', 'Trial Balance')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-balance-scale me-2"></i>Trial Balance</h6>
    </div>

    <div class="filter-wrapper">
    <form method="GET">
        <div class="row g-2 align-items-end">
            <div class="col-md-4 mb-3">
                <label for="date" class="form-label">As of Date</label>
                <input type="date" class="form-control" name="date" value="{{ $date }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-2 mb-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Generate</button>
            </div>
        </div>
    </form>
    </div>

    <div class="p-4">
        <div class="row mb-3">
            <div class="col-md-8">
                <h5 class="mb-0">Trial Balance as of {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h5>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('accounting.trial-balance.export', ['format' => 'csv'] + request()->query()) }}"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                        <li><a class="dropdown-item" href="{{ route('accounting.trial-balance.export', ['format' => 'excel'] + request()->query()) }}"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                        <li><a class="dropdown-item" href="{{ route('accounting.trial-balance.export', ['format' => 'pdf'] + request()->query()) }}"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Account Code</th>
                        <th>Account Name</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trialBalance as $item)
                        <tr>
                            <td><code>{{ $item['account']->code }}</code></td>
                            <td>{{ $item['account']->name }}</td>
                            <td class="text-end {{ $item['debit'] > 0 ? 'text-success fw-semibold' : 'text-muted' }}">{{ $item['debit'] > 0 ? '৳' . number_format($item['debit'], 2) : '—' }}</td>
                            <td class="text-end {{ $item['credit'] > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">{{ $item['credit'] > 0 ? '৳' . number_format($item['credit'], 2) : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">No accounts with balances found.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <th colspan="2" class="text-end">Total:</th>
                        <th class="text-end">৳{{ number_format($totalDebit, 2) }}</th>
                        <th class="text-end">৳{{ number_format($totalCredit, 2) }}</th>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end"><strong>Difference:</strong></td>
                        <td colspan="2" class="text-end">
                            <strong class="{{ abs($totalDebit - $totalCredit) < 0.01 ? 'text-success' : 'text-danger' }}">
                                ৳{{ number_format(abs($totalDebit - $totalCredit), 2) }}
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

