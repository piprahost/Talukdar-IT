<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Balance - {{ $date }}</title>
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
        
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
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
        
        tfoot {
            border-top: 2px solid #333;
        }
        
        tfoot th, tfoot td {
            background-color: #f0f0f0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        @php
            $company = \App\Http\Controllers\CompanyInfoController::getCompanySettings();
        @endphp
        <h1>{{ $company->company_name ?? 'ERP System' }}</h1>
    </div>
    
    <div class="report-title">Trial Balance</div>
    <p><strong>As of:</strong> {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Account Code</th>
                <th>Account Name</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trialBalance as $item)
                <tr>
                    <td><code>{{ $item['account']->code }}</code></td>
                    <td>{{ $item['account']->name }}</td>
                    <td class="text-right">{{ $item['debit'] > 0 ? '৳' . number_format($item['debit'], 2) : '-' }}</td>
                    <td class="text-right">{{ $item['credit'] > 0 ? '৳' . number_format($item['credit'], 2) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">Total:</th>
                <th class="text-right">৳{{ number_format($totalDebit, 2) }}</th>
                <th class="text-right">৳{{ number_format($totalCredit, 2) }}</th>
            </tr>
            <tr>
                <td colspan="2" class="text-right"><strong>Difference:</strong></td>
                <td colspan="2" class="text-right">
                    <strong>{{ abs($totalDebit - $totalCredit) < 0.01 ? '' : '⚠️ ' }}৳{{ number_format(abs($totalDebit - $totalCredit), 2) }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>
    
    <div style="margin-top: 20px; text-align: right; font-size: 8pt; color: #666;">
        Generated on: {{ now()->format('d M Y, h:i A') }}
    </div>
</body>
</html>

