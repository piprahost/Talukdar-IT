<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $sale->invoice_number }}</title>
    <style>
        @page {
            margin: 0.7cm;
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
            background: white;
        }
        
        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 0;
            background: white;
        }
        
        .print-header {
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .company-info h1 {
            color: #000;
            font-size: 18pt;
            margin-bottom: 3px;
            font-weight: bold;
        }
        
        .company-info p {
            font-size: 8pt;
            margin: 1px 0;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-info h2 {
            font-size: 16pt;
            margin-bottom: 5px;
            color: #000;
        }
        
        .invoice-info p {
            font-size: 8pt;
            margin: 2px 0;
        }
        
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin: 12px 0;
            padding: 8px 0;
            border-bottom: 1px dotted #666;
        }
        
        .invoice-details > div {
            flex: 1;
        }
        
        .invoice-details h3 {
            font-size: 9pt;
            margin-bottom: 4px;
            color: #000;
        }
        
        .invoice-details p {
            font-size: 8pt;
            margin: 2px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        table thead {
            background: #f5f5f5;
        }
        
        table th {
            padding: 6px 4px;
            text-align: left;
            font-size: 8pt;
            border-bottom: 1px solid #333;
            font-weight: bold;
        }
        
        table td {
            padding: 5px 4px;
            font-size: 8pt;
            border-bottom: 1px dotted #ccc;
        }
        
        table tfoot {
            border-top: 2px solid #000;
        }
        
        table tfoot th {
            padding: 6px 4px;
            text-align: right;
            font-size: 9pt;
            border-bottom: none;
        }
        
        .total-row {
            font-weight: bold;
            font-size: 10pt;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #333;
            font-size: 8pt;
            text-align: center;
        }
        
        @media print {
            body {
                background: white;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        @php
            $company = \App\Http\Controllers\CompanyInfoController::getCompanySettings();
        @endphp
        
        <div class="print-header">
            <div class="header-top">
                <div class="company-info">
                    <h1>{{ $company->company_name ?? 'Company Name' }}</h1>
                    @if($company->address ?? null)<p>{{ $company->address }}</p>@endif
                    @if($company->phone ?? null)<p>Phone: {{ $company->phone }}</p>@endif
                    @if($company->email ?? null)<p>Email: {{ $company->email }}</p>@endif
                </div>
                <div class="invoice-info">
                    <h2>INVOICE</h2>
                    <p><strong>Invoice #:</strong> {{ $sale->invoice_number }}</p>
                    <p><strong>Date:</strong> {{ $sale->sale_date->format('d M Y') }}</p>
                </div>
            </div>
        </div>
        
        <div class="invoice-details">
            <div>
                <h3>Bill To:</h3>
                <p><strong>{{ $sale->customer ? $sale->customer->name : ($sale->customer_name ?? 'Walk-in Customer') }}</strong></p>
                @if($sale->customer_phone)<p>Phone: {{ $sale->customer_phone }}</p>@endif
                @if($sale->customer_address)<p>{{ $sale->customer_address }}</p>@endif
            </div>
            <div>
                <h3>Payment Information:</h3>
                <p><strong>Status:</strong> {{ ucfirst($sale->payment_status) }}</p>
                <p><strong>Paid:</strong> ৳{{ number_format($sale->paid_amount, 2) }}</p>
                <p><strong>Due:</strong> ৳{{ number_format($sale->due_amount, 2) }}</p>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 30%;">Product</th>
                    <th style="width: 12%;">Barcode</th>
                    <th style="width: 8%;">Qty</th>
                    <th style="width: 10%;">Unit Price</th>
                    <th style="width: 8%;">Discount</th>
                    <th style="width: 10%;">Warranty</th>
                    <th style="width: 18%; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td><code>{{ $item->barcode ?? 'N/A' }}</code></td>
                        <td>{{ $item->quantity }}</td>
                        <td>৳{{ number_format($item->unit_price, 2) }}</td>
                        <td>৳{{ number_format($item->discount, 2) }}</td>
                        <td>
                            @if($item->product->warranty_period && $item->product->warranty_period > 0)
                                {{ $item->product->warranty_period }} days
                            @else
                                N/A
                            @endif
                        </td>
                        <td style="text-align: right;">৳{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" style="text-align: right;">Subtotal:</th>
                    <th style="text-align: right;">৳{{ number_format($sale->subtotal, 2) }}</th>
                </tr>
                @if($sale->tax_amount > 0)
                <tr>
                    <th colspan="7" style="text-align: right;">Tax:</th>
                    <th style="text-align: right;">৳{{ number_format($sale->tax_amount, 2) }}</th>
                </tr>
                @endif
                @if($sale->discount_amount > 0)
                <tr>
                    <th colspan="7" style="text-align: right;">Discount:</th>
                    <th style="text-align: right;">৳{{ number_format($sale->discount_amount, 2) }}</th>
                </tr>
                @endif
                <tr class="total-row">
                    <th colspan="7" style="text-align: right;">Total Amount:</th>
                    <th style="text-align: right;">৳{{ number_format($sale->total_amount, 2) }}</th>
                </tr>
            </tfoot>
        </table>
        
        @if($sale->notes)
        <div style="margin-top: 10px; padding: 5px; font-size: 8pt;">
            <strong>Notes:</strong> {{ $sale->notes }}
        </div>
        @endif
        
        <div class="footer">
            <p>Thank you for your business!</p>
            <p style="margin-top: 5px;">This is a computer-generated invoice.</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
        <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary">Back</a>
    </div>
</body>
</html>

