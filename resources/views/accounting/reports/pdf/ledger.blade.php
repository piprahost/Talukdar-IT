<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger Report - {{ $account->code }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #000;
        }
        
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 8pt;
            color: #666;
        }
        
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
            padding: 6px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            border-bottom: 2px solid #333;
        }
        
        .text-right {
            text-align: right;
        }
        
        .summary-box {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        
        .summary-box strong {
            display: inline-block;
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="header">
        @php
            $company = \App\Http\Controllers\CompanyInfoController::getCompanySettings();
        @endphp
        <h1>{{ $company->company_name ?? 'ERP System' }}</h1>
        <p>{{ $company->address ?? '' }} {{ $company->city ?? '' }}, {{ $company->country ?? 'Bangladesh' }}</p>
        <p>{{ $company->phone ?? '' }} {{ $company->email ? '| ' . $company->email : '' }}</p>
    </div>
    
    <div class="report-title">Ledger Report</div>
    <p><strong>Account:</strong> {{ $account->code }} - {{ $account->name }}</p>
    <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</p>
    
    <div class="summary-box">
        <div><strong>Opening Balance:</strong> ৳{{ number_format($openingBalance, 2) }}</div>
        <div><strong>Closing Balance:</strong> ৳{{ number_format($closingBalance, 2) }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Entry #</th>
                <th>Description</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #f0f0f0;">
                <td colspan="5" class="text-right"><strong>Opening Balance</strong></td>
                <td class="text-right"><strong>৳{{ number_format($openingBalance, 2) }}</strong></td>
            </tr>
            @php
                $runningBalance = $openingBalance;
            @endphp
            @foreach($entries as $item)
                @php
                    if($account->balance_type === 'debit') {
                        $runningBalance += $item->debit - $item->credit;
                    } else {
                        $runningBalance += $item->credit - $item->debit;
                    }
                @endphp
                <tr>
                    <td>{{ $item->journalEntry->entry_date->format('d M Y') }}</td>
                    <td>{{ $item->journalEntry->entry_number }}</td>
                    <td>{{ $item->description ?? $item->journalEntry->description }}</td>
                    <td class="text-right">{{ $item->debit > 0 ? '৳' . number_format($item->debit, 2) : '-' }}</td>
                    <td class="text-right">{{ $item->credit > 0 ? '৳' . number_format($item->credit, 2) : '-' }}</td>
                    <td class="text-right"><strong>৳{{ number_format($runningBalance, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 20px; text-align: right; font-size: 8pt; color: #666;">
        Generated on: {{ now()->format('d M Y, h:i A') }}
    </div>
</body>
</html>

