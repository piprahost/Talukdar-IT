@extends('layouts.dashboard')

@section('title', 'Balance Sheet')
@section('page-title', 'Balance Sheet')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-file-invoice-dollar me-2"></i>Balance Sheet</h6>
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
        <div class="row g-3 mb-4 module-stats">
            <div class="col-md-4">
                <div class="module-stat-card rounded-3 p-3" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-left:4px solid #3b82f6;">
                    <div class="small text-muted text-uppercase fw-bold">Total Assets</div>
                    <div class="fw-bold fs-5" style="color:#1d4ed8;">৳{{ number_format(abs($totalAssets), 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="module-stat-card rounded-3 p-3" style="background:linear-gradient(135deg,#fef2f2,#fee2e2);border-left:4px solid #ef4444;">
                    <div class="small text-muted text-uppercase fw-bold">Total Liabilities</div>
                    <div class="fw-bold fs-5" style="color:#991b1b;">৳{{ number_format(abs($totalLiabilities), 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="module-stat-card rounded-3 p-3" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-left:4px solid #16a34a;">
                    <div class="small text-muted text-uppercase fw-bold">Net Position</div>
                    <div class="fw-bold fs-5" style="color:#166534;">৳{{ number_format(abs($totalAssets - $totalLiabilities), 2) }}</div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-8">
                <h4 class="mb-2">Balance Sheet</h4>
                <p class="text-muted mb-0">As of {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('accounting.balance-sheet.export', ['format' => 'csv'] + request()->query()) }}"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                        <li><a class="dropdown-item" href="{{ route('accounting.balance-sheet.export', ['format' => 'excel'] + request()->query()) }}"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                        <li><a class="dropdown-item" href="{{ route('accounting.balance-sheet.export', ['format' => 'pdf'] + request()->query()) }}"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <h5 class="border-bottom pb-2 mb-3">ASSETS</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $item)
                                <tr>
                                    <td>{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                                    <td class="text-end">৳{{ number_format(abs($item['balance']), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">No assets</td></tr>
                            @endforelse
                            <tr class="table-secondary">
                                <th>Total Assets</th>
                                <th class="text-end">৳{{ number_format(abs($totalAssets), 2) }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="col-md-6">
                <h5 class="border-bottom pb-2 mb-3">LIABILITIES</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($liabilities as $item)
                                <tr>
                                    <td>{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                                    <td class="text-end">৳{{ number_format(abs($item['balance']), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">No liabilities</td></tr>
                            @endforelse
                            <tr class="table-secondary">
                                <th>Total Liabilities</th>
                                <th class="text-end">৳{{ number_format(abs($totalLiabilities), 2) }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <h5 class="border-bottom pb-2 mb-3 mt-4">EQUITY</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equity as $item)
                                <tr>
                                    <td>{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                                    <td class="text-end">৳{{ number_format(abs($item['balance']), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">No equity accounts</td></tr>
                            @endforelse
                            <tr>
                                <td><strong>Retained Earnings</strong></td>
                                <td class="text-end"><strong>৳{{ number_format($retainedEarnings, 2) }}</strong></td>
                            </tr>
                            <tr class="table-secondary">
                                <th>Total Equity</th>
                                <th class="text-end">৳{{ number_format(abs($totalEquity), 2) }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <table class="table table-bordered">
                        <tr>
                            <th>Total Liabilities + Equity</th>
                            <th class="text-end">৳{{ number_format(abs($totalLiabilities + $totalEquity), 2) }}</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

