@extends('layouts.dashboard')

@section('title', 'Profit & Loss Statement')
@section('page-title', 'Profit & Loss Statement')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="fas fa-chart-line me-2"></i>Profit & Loss Statement</h6>
    </div>
    
    <form method="GET" class="p-4 border-bottom">
        <div class="row">
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
    
    <div class="p-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h4 class="mb-2">Profit & Loss Statement</h4>
                <p class="text-muted mb-0">
                    Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('accounting.profit-loss.export', ['format' => 'csv'] + request()->query()) }}"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                        <li><a class="dropdown-item" href="{{ route('accounting.profit-loss.export', ['format' => 'excel'] + request()->query()) }}"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                        <li><a class="dropdown-item" href="{{ route('accounting.profit-loss.export', ['format' => 'pdf'] + request()->query()) }}"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <h5 class="border-bottom pb-2 mb-3">REVENUE</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($revenue as $item)
                                <tr>
                                    <td>{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                                    <td class="text-end">৳{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">No revenue</td></tr>
                            @endforelse
                            <tr class="table-secondary">
                                <th>Total Revenue</th>
                                <th class="text-end">৳{{ number_format($totalRevenue, 2) }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="col-md-6">
                <h5 class="border-bottom pb-2 mb-3">EXPENSES</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $item)
                                <tr>
                                    <td>{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                                    <td class="text-end">৳{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">No expenses</td></tr>
                            @endforelse
                            <tr class="table-secondary">
                                <th>Total Expenses</th>
                                <th class="text-end">৳{{ number_format($totalExpenses, 2) }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        {{-- Net Profit / Loss Summary --}}
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="module-stat-card" style="border-left:3px solid #16a34a;">
                    <div class="msc-label">Total Revenue</div>
                    <div class="msc-value" style="color:#16a34a;font-size:20px;">৳{{ number_format($totalRevenue, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="module-stat-card" style="border-left:3px solid #ef4444;">
                    <div class="msc-label">Total Expenses</div>
                    <div class="msc-value" style="color:#ef4444;font-size:20px;">৳{{ number_format($totalExpenses, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="module-stat-card" style="border-left:4px solid {{ $netProfit >= 0 ? '#16a34a' : '#ef4444' }};background:{{ $netProfit >= 0 ? '#f0fdf4' : '#fef2f2' }};">
                    <div class="msc-label">{{ $netProfit >= 0 ? '✓ Net Profit' : '✗ Net Loss' }}</div>
                    <div class="msc-value" style="color:{{ $netProfit >= 0 ? '#16a34a' : '#ef4444' }};font-size:22px;">৳{{ number_format(abs($netProfit), 2) }}</div>
                    @if($totalRevenue > 0)
                    <div class="msc-sub">{{ number_format(abs($netProfit) / $totalRevenue * 100, 1) }}% {{ $netProfit >= 0 ? 'margin' : 'loss ratio' }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

