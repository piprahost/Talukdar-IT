<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Order - {{ $service->service_number }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        @page {
            margin: 0.7cm;
            size: A4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        body {
            font-family: 'DM Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.5;
            color: #333;
            background: white;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 0;
            background: white;
        }
        
        /* Header - Modern & Colorful */
        .print-header {
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .header-left {
            width: 60%;
        }
        
        .header-right {
            width: 40%;
            text-align: right;
        }
        
        .company-name {
            font-size: 24pt;
            font-weight: 800;
            color: #16a34a;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            margin-bottom: 5px;
        }
        
        .company-tagline {
            font-size: 9pt;
            color: #15803d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        
        .company-address {
            font-size: 9pt;
            color: #555;
            line-height: 1.5;
        }
        
        .invoice-title {
            font-size: 32pt;
            font-weight: 700;
            color: #16a34a;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        
        .invoice-details-box {
            background-color: #16a34a;
            color: white;
            padding: 8px 15px;
            display: inline-block;
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Bill To Section */
        .bill-to-section {
            margin-bottom: 30px;
        }
        
        .bill-to-label {
            background-color: #16a34a;
            color: white;
            padding: 5px 10px;
            font-size: 9pt;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .customer-name {
            font-size: 12pt;
            font-weight: 700;
            color: #111;
            margin-bottom: 4px;
        }
        
        .customer-details {
            font-size: 9pt;
            color: #555;
            line-height: 1.5;
        }
        
        /* Table - Modern */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        
        .table th {
            background-color: #16a34a;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table th:last-child {
            text-align: right;
        }
        
        .table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ecf0f1;
            color: #111;
            font-size: 9.5pt;
        }
        
        .table td:last-child {
            text-align: right;
            font-weight: 600;
        }
        
        .table tr:nth-child(even) {
            background-color: #f9fbfb;
        }
        
        /* Totals Section */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }
        
        .totals-table {
            width: 40%;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 8px 0;
            font-size: 9.5pt;
            color: #111;
        }
        
        .totals-table tr:last-child td {
            padding-top: 12px;
            border-top: 2px solid #16a34a;
            font-weight: 800;
            font-size: 11pt;
            color: #111;
        }
        
        .totals-table .label {
            text-align: left;
            font-weight: 600;
            color: #7f8c8d;
        }
        
        .totals-table .value {
            text-align: right;
        }
        
        .total-highlight {
            background-color: #16a34a;
            color: white !important;
            padding: 10px 15px !important;
        }
        
        .total-row .label {
            padding-left: 15px;
        }
        
        /* Footer/Thank You */
        .footer-note {
            background-color: #16a34a;
            color: white;
            padding: 10px 15px;
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
            text-align: left;
            margin-top: 20px;
        }
        
        .terms-text {
            font-size: 8pt;
            color: #7f8c8d;
            margin-top: 10px;
            line-height: 1.4;
        }
        
        .signature-area {
            text-align: right;
            margin-top: 40px;
        }
        
        .signature-line {
            display: inline-block;
            width: 200px;
            border-top: 1px solid #16a34a;
            padding-top: 5px;
            text-align: center;
            font-size: 9pt;
            color: #16a34a;
        }
        
        /* Badge for Status */
        .status-badge-modern {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 5px;
        }
        
        .status-paid { background-color: #dcfce7; color: #166534; }
        .status-unpaid { background-color: #fee2e2; color: #991b1b; }
        .status-partial { background-color: #fef9c3; color: #854d0e; }
        
        /* Divider Lines */
        .divider {
            height: 1px;
            background: #ddd;
            margin: 12px 0;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .print-container {
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .section {
                page-break-inside: avoid;
            }
            
            .signature-section {
                page-break-inside: avoid;
                page-break-after: avoid;
            }
            
            .print-footer {
                page-break-inside: avoid;
            }
            
            @page {
                margin: 0.7cm;
            }
        }
        
        .print-actions {
            text-align: center;
            padding: 15px;
            background: #f5f5f5;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .btn {
            padding: 10px 20px;
            margin: 5px;
            border: 1px solid #000;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            background: white;
            color: #000;
        }
        
        .btn:hover {
            background: #f5f5f5;
        }
        
        .btn-primary {
            background: #000;
            color: white;
        }
        
        .btn-primary:hover {
            background: #333;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Print Actions -->
        <div class="print-actions no-print">
            <button onclick="window.print()" class="btn btn-primary">Print</button>
            <a href="{{ route('services.show', $service) }}" class="btn">Back</a>
        </div>
        
        <!-- Header -->
        <div class="print-header">
            <div class="header-left">
                <div class="company-info">
                    @php
                        $company = \App\Http\Controllers\CompanyInfoController::getCompanySettings();
                    @endphp
                    <div class="company-name">{{ $company->company_name ?? 'ERP System' }}</div>
                    @if($company->service_center_name)
                        <div class="company-tagline">{{ $company->service_center_name }}</div>
                    @endif
                    
                    <div class="company-address">
                        @if($company->address)
                            {{ $company->address }}<br>
                        @endif
                        {{ $company->city ?? 'Dhaka' }}{{ $company->country ? ', ' . $company->country : ', Bangladesh' }}
                        @if($company->phone || $company->email)
                            <br>
                            @if($company->phone)Phone: {{ $company->phone }}@endif
                            @if($company->phone && $company->email) | @endif
                            @if($company->email)Email: {{ $company->email }}@endif
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="header-right">
                <div class="invoice-title">SERVICE MEMO</div>
                <div class="invoice-details-box">
                    # {{ $service->service_number }}
                </div>
                <div style="margin-top: 5px; font-size: 9pt; color: #555;">
                    Date: {{ $service->receive_date->format('F d, Y') }}
                </div>
            </div>
        </div>
        
        <!-- Bill To Section -->
        <div class="bill-to-section">
            <div class="bill-to-label">Bill To</div>
            <div class="customer-name">{{ $service->customer_name }}</div>
            <div class="customer-details">
                {{ $service->customer_phone }}<br>
                @if($service->customer_address)
                    {{ $service->customer_address }}
                @endif
            </div>
        </div>
        
        <!-- Product Information Table -->
        <div class="section-title" style="border: none; margin-bottom: 10px;">Product & Service Details</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Item Description</th>
                    <th style="width: 25%;">Problem / Notes</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 20%; text-align: right;">Cost</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <strong>{{ $service->product_name }}</strong>
                        @if($service->serial_number)
                            <br><small style="color: #666;">SN: {{ $service->serial_number }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $service->problem_notes ?? 'N/A' }}
                        @if($service->service_notes)
                            <br><small style="color: #666;">Note: {{ $service->service_notes }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge-modern 
                            @if($service->status == 'completed' || $service->status == 'delivered') status-paid 
                            @elseif($service->status == 'cancelled') status-unpaid 
                            @else status-partial @endif">
                            {{ ucfirst(str_replace('_', ' ', $service->status)) }}
                        </span>
                    </td>
                    <td>৳{{ number_format($service->service_cost, 2) }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Totals Section -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">৳{{ number_format($service->service_cost, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Paid Amount</td>
                    <td class="value" style="color: #166534;">(-) ৳{{ number_format($service->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Due Amount</td>
                    <td class="value" style="color: #991b1b;">৳{{ number_format($service->due_amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label">TOTAL DUE</td>
                    <td class="value total-highlight">৳{{ number_format($service->due_amount, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Footer / Terms -->
        <div class="footer-note">Terms & Conditions</div>
        @php
            $companyTerms = \App\Http\Controllers\CompanyInfoController::getCompanySettings();
        @endphp
        <div class="terms-text">
            {{ $companyTerms->terms_and_conditions ?? 'Thank you for your business. Please contact us for any queries regarding this invoice.' }}
        </div>
        
        <!-- Signature -->
        <div class="signature-area">
            <div class="signature-line">Authorized Signature</div>
        </div>
        
        <div style="margin-top: 30px; text-align: center; font-size: 8pt; color: #999;">
            This is a computer generated document.
        </div>
    </div>
</body>
</html>
