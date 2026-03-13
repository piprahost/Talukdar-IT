<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit & Loss - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 landscape;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 8pt;
            line-height: 1.4;
            color: #000;
        }
        
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .two-column {
            display: table;
            width: 100%;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
            padding: 5px;
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
        
        .section-title {
            font-weight: bold;
            font-size: 10pt;
            margin-top: 15px;
            margin-bottom: 5px;
            border-bottom: 1px solid #333;
            padding-bottom: 3px;
        }
        
        .net-profit {
            margin-top: 20px;
            padding: 10px;
            text-align: center;
            border: 2px solid #333;
            background-color: {{ $netProfit >= 0 ? '#d4edda' : '#f8d7da' }};
        }
    </style>
</head>
<body>
    <div class="header">
        @php
            $company = \App\Http\Controllers\CompanyInfoController::getCompanySettings();
        @endphp
        <h1>{{ $company->company_name ?? 'ERP System' }}</h1>
        <p>Profit & Loss Statement</p>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</p>
    </div>
    
    <div class="two-column">
        <div class="column">
            <div class="section-title">REVENUE</div>
            <table>
                <thead>
                    <tr>
                        <th>Account</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($revenue as $item)
                        <tr>
                            <td>{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                            <td class="text-right">৳{{ number_format($item['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #f0f0f0;">
                        <th>Total Revenue</th>
                        <th class="text-right">৳{{ number_format($totalRevenue, 2) }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="column">
            <div class="section-title">EXPENSES</div>
            <table>
                <thead>
                    <tr>
                        <th>Account</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $item)
                        <tr>
                            <td>{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                            <td class="text-right">৳{{ number_format($item['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #f0f0f0;">
                        <th>Total Expenses</th>
                        <th class="text-right">৳{{ number_format($totalExpenses, 2) }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="net-profit">
        <h3 style="margin: 0; font-size: 14pt;">
            {{ $netProfit >= 0 ? 'Net Profit' : 'Net Loss' }}: 
            ৳{{ number_format(abs($netProfit), 2) }}
        </h3>
    </div>
    
    <div style="margin-top: 20px; text-align: right; font-size: 8pt; color: #666;">
        Generated on: {{ now()->format('d M Y, h:i A') }}
    </div>
</body>
</html>

