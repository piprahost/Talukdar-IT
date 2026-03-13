<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Order - {{ $service->service_number }}</title>
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
        
        /* Header - Clean and Minimal */
        .print-header {
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
            margin-bottom: 10px;
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
            font-weight: 700;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        
        .company-info p {
            font-size: 8pt;
            color: #333;
            margin: 1px 0;
            line-height: 1.3;
        }
        
        .service-number {
            text-align: right;
        }
        
        .service-number h2 {
            color: #000;
            font-size: 14pt;
            margin-bottom: 3px;
            font-weight: 700;
            letter-spacing: 1px;
            line-height: 1.2;
        }
        
        .service-number p {
            font-size: 8pt;
            color: #333;
            margin: 1px 0;
            line-height: 1.3;
        }
        
        /* Layout */
        .row {
            display: flex;
            gap: 12px;
            margin-bottom: 8px;
        }
        
        .col-6 {
            width: 50%;
        }
        
        .col-12 {
            width: 100%;
        }
        
        /* Sections - Clean and Minimal */
        .section {
            margin-bottom: 8px;
        }
        
        .section-title {
            color: #000;
            padding: 4px 0;
            font-size: 10pt;
            font-weight: 700;
            margin: 0 0 5px 0;
            border-bottom: 1px solid #ddd;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }
        
        .section-content {
            padding: 0;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
            padding-bottom: 4px;
            font-size: 9pt;
            border-bottom: 1px dotted #e0e0e0;
            line-height: 1.4;
        }
        
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-label {
            font-weight: 600;
            width: 38%;
            color: #333;
        }
        
        .info-value {
            width: 62%;
            color: #000;
        }
        
        /* Table - Minimal Borders */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0 0 0;
            font-size: 9pt;
        }
        
        .table th {
            background: transparent;
            color: #000;
            padding: 5px 0;
            text-align: left;
            font-weight: 700;
            border-bottom: 1px solid #333;
            text-transform: uppercase;
            font-size: 8.5pt;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }
        
        .table td {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
            color: #000;
            line-height: 1.4;
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Status Badge - Minimal */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border: 1px solid #ccc;
            font-weight: 600;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }
        
        .status-pending { 
            border-color: #999;
            background: #f5f5f5;
        }
        .status-in_progress { 
            border-color: #999;
            background: #f0f0f0;
        }
        .status-completed { 
            border-color: #666;
            background: #e8e8e8;
        }
        .status-delivered { 
            border-color: #666;
            background: #e8e8e8;
        }
        .status-cancelled { 
            border-color: #999;
            background: #f5f5f5;
        }
        
        /* Payment Summary - Clean */
        .payment-summary {
            padding: 6px 0;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 9pt;
            border-bottom: 1px dotted #e0e0e0;
            line-height: 1.4;
        }
        
        .payment-row:not(.payment-total) {
            border-bottom: 1px dotted #e0e0e0;
        }
        
        .payment-total {
            font-size: 10pt;
            font-weight: 700;
            border-top: 2px solid #333;
            border-bottom: none;
            padding-top: 6px;
            margin-top: 4px;
        }
        
        /* Notes - Clean */
        .notes-box {
            padding: 6px 0;
            font-size: 9pt;
            line-height: 1.5;
            color: #333;
            border-left: 2px solid #ddd;
            padding-left: 10px;
            margin-top: 4px;
        }
        
        /* Terms - Clean */
        .terms-box {
            padding: 6px 0;
            font-size: 8pt;
            line-height: 1.5;
            color: #555;
            white-space: pre-line;
            border-left: 2px solid #ddd;
            padding-left: 10px;
            margin-top: 4px;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        
        .signature-box {
            width: 48%;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 25px;
            padding-top: 4px;
            font-weight: 600;
            font-size: 8.5pt;
        }
        
        /* Footer */
        .print-footer {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-size: 8pt;
            color: #666;
            text-align: center;
            line-height: 1.4;
        }
        
        .print-footer strong {
            font-weight: 700;
            color: #000;
        }
        
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
            <div class="header-top">
                <div class="company-info">
                    @php
                        $company = \App\Http\Controllers\CompanyInfoController::getCompanySettings();
                    @endphp
                    <h1>{{ $company->company_name ?? 'ERP System' }}</h1>
                    @if($company->service_center_name)
                        <p><strong>{{ $company->service_center_name }}</strong></p>
                    @endif
                    @if($company->address)
                        <p>{{ $company->address }}</p>
                    @endif
                    <p>{{ $company->city ?? 'Dhaka' }}{{ $company->country ? ', ' . $company->country : ', Bangladesh' }}</p>
                    @if($company->phone || $company->email)
                        <p>
                            @if($company->phone){{ $company->phone }}@endif
                            @if($company->phone && $company->email) | @endif
                            @if($company->email){{ $company->email }}@endif
                        </p>
                    @endif
                </div>
                <div class="service-number">
                    <h2>SERVICE ORDER</h2>
                    <p><strong>#:</strong> {{ $service->service_number }}</p>
                    <p><strong>Date:</strong> {{ $service->receive_date->format('d M Y') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Customer & Status -->
        <div class="row">
            <div class="col-6">
                <div class="section">
                    <div class="section-title">Customer Information</div>
                    <div class="section-content">
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value"><strong>{{ $service->customer_name }}</strong></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $service->customer_phone }}</span>
                        </div>
                        @if($service->customer_address)
                        <div class="info-row">
                            <span class="info-label">Address:</span>
                            <span class="info-value">{{ $service->customer_address }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-6">
                <div class="section">
                    <div class="section-title">Service Details</div>
                    <div class="section-content">
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                <span class="status-badge status-{{ $service->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $service->status)) }}
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Receive Date:</span>
                            <span class="info-value">{{ $service->receive_date->format('d M Y') }}</span>
                        </div>
                        @if($service->delivery_date)
                        <div class="info-row">
                            <span class="info-label">Delivery Date:</span>
                            <span class="info-value">{{ $service->delivery_date->format('d M Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Information -->
        <div class="section">
            <div class="section-title">Product Information</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Serial Number / Barcode</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>{{ $service->product_name }}</strong></td>
                        <td>{{ $service->serial_number ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Problem & Service Notes -->
        @if($service->problem_notes || $service->service_notes)
        <div class="row">
            @if($service->problem_notes)
            <div class="{{ $service->service_notes ? 'col-6' : 'col-12' }}">
                <div class="section">
                    <div class="section-title">Problem Description</div>
                    <div class="section-content">
                        <div class="notes-box">{{ $service->problem_notes }}</div>
                    </div>
                </div>
            </div>
            @endif
            
            @if($service->service_notes)
            <div class="{{ $service->problem_notes ? 'col-6' : 'col-12' }}">
                <div class="section">
                    <div class="section-title">Service Notes</div>
                    <div class="section-content">
                        <div class="notes-box">{{ $service->service_notes }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
        
        <!-- Payment Summary -->
        <div class="section">
            <div class="section-title">Payment Summary</div>
            <div class="section-content">
                <div class="payment-summary">
                    <div class="payment-row">
                        <span>Service Cost (BDT):</span>
                        <span><strong>৳{{ number_format($service->service_cost, 2) }}</strong></span>
                    </div>
                    <div class="payment-row">
                        <span>Paid Amount (BDT):</span>
                        <span><strong>৳{{ number_format($service->paid_amount, 2) }}</strong></span>
                    </div>
                    <div class="payment-row">
                        <span>Due Amount (BDT):</span>
                        <span><strong>৳{{ number_format($service->due_amount, 2) }}</strong></span>
                    </div>
                    <div class="payment-row payment-total">
                        <span>Payment Status:</span>
                        <span>
                            @if($service->due_amount == 0 && $service->paid_amount > 0)
                                <span class="status-badge status-completed">Fully Paid</span>
                            @elseif($service->paid_amount == 0)
                                <span class="status-badge status-pending">Unpaid</span>
                            @else
                                <span class="status-badge status-in_progress">Partial Payment</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Terms and Conditions -->
        @php
            $companyTerms = \App\Http\Controllers\CompanyInfoController::getCompanySettings();
        @endphp
        @if($companyTerms && $companyTerms->terms_and_conditions)
        <div class="section">
            <div class="section-title">Terms & Conditions</div>
            <div class="section-content">
                <div class="terms-box">{{ $companyTerms->terms_and_conditions }}</div>
            </div>
        </div>
        @endif
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    <strong>Customer Signature</strong>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <strong>Service Center Signature</strong>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="print-footer">
            @php
                $company = \App\Http\Controllers\CompanyInfoController::getCompanySettings();
            @endphp
            <p><strong>Thank you for choosing our service!</strong></p>
            @if($company && $company->email)
                <p>For inquiries: {{ $company->email }}</p>
            @elseif($service->creator && $service->creator->email)
                <p>For inquiries: {{ $service->creator->email }}</p>
            @endif
            <p style="margin-top: 4px;">Computer-generated document. No signature required.</p>
        </div>
    </div>
</body>
</html>
