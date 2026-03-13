<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warranty Memo - {{ $warrantySubmission->memo_number }}</title>
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
        
        .memo-info {
            text-align: right;
        }
        
        .memo-info h2 {
            font-size: 16pt;
            margin-bottom: 5px;
            color: #000;
        }
        
        .memo-info p {
            font-size: 8pt;
            margin: 2px 0;
        }
        
        .info-section {
            margin: 12px 0;
            padding: 8px 0;
            border-bottom: 1px dotted #666;
        }
        
        .info-section h3 {
            font-size: 9pt;
            margin-bottom: 4px;
            color: #000;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .info-section p {
            font-size: 8pt;
            margin: 2px 0;
        }
        
        .details-table {
            width: 100%;
            margin: 10px 0;
        }
        
        .details-table th {
            padding: 6px 4px;
            text-align: left;
            font-size: 8pt;
            border-bottom: 1px solid #333;
            font-weight: bold;
            width: 30%;
        }
        
        .details-table td {
            padding: 5px 4px;
            font-size: 8pt;
            border-bottom: 1px dotted #ccc;
        }
        
        .notes-box {
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #333;
            font-size: 8pt;
            min-height: 60px;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #333;
            font-size: 8pt;
            text-align: center;
        }
        
        .signature-section {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 5px;
            font-size: 8pt;
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
                    @if($company->address)
                        <p>{{ $company->address }}</p>
                    @endif
                    <p>{{ $company->city ?? 'Dhaka' }}{{ $company->country ? ', ' . $company->country : ', Bangladesh' }}</p>
                    @if($company->phone)
                        <p>Phone: {{ $company->phone }}</p>
                    @endif
                    @if($company->email)
                        <p>Email: {{ $company->email }}</p>
                    @endif
                </div>
                <div class="memo-info">
                    <h2>WARRANTY MEMO</h2>
                    <p><strong>Memo #:</strong> {{ $warrantySubmission->memo_number }}</p>
                    <p><strong>Date:</strong> {{ $warrantySubmission->submission_date->format('d M Y') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Customer Information -->
        <div class="info-section">
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> {{ $warrantySubmission->customer_name }}</p>
            <p><strong>Phone:</strong> {{ $warrantySubmission->customer_phone }}</p>
            @if($warrantySubmission->customer_address)
                <p><strong>Address:</strong> {{ $warrantySubmission->customer_address }}</p>
            @endif
        </div>
        
        <!-- Product & Warranty Information -->
        <div class="info-section">
            <h3>Product & Warranty Details</h3>
            <table class="details-table">
                <tr>
                    <th>Barcode:</th>
                    <td><code>{{ $warrantySubmission->barcode ?? 'N/A' }}</code></td>
                </tr>
                <tr>
                    <th>Product Name:</th>
                    <td><strong>{{ $warrantySubmission->product->name }}</strong></td>
                </tr>
                @if($warrantySubmission->warranty)
                <tr>
                    <th>Warranty Status:</th>
                    <td>
                        @if($warrantySubmission->warranty->isActive())
                            <strong>Active</strong> (Expires: {{ $warrantySubmission->warranty->end_date->format('d M Y') }})
                        @else
                            <strong>Expired</strong>
                        @endif
                    </td>
                </tr>
                @endif
                @if($warrantySubmission->sale)
                <tr>
                    <th>Original Invoice:</th>
                    <td>{{ $warrantySubmission->sale->invoice_number }}</td>
                </tr>
                @endif
                <tr>
                    <th>Physical Condition:</th>
                    <td>{{ ucfirst($warrantySubmission->condition) }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Problem Description -->
        <div class="info-section">
            <h3>Problem Description</h3>
            <div class="notes-box">
                {{ $warrantySubmission->problem_description }}
            </div>
        </div>
        
        <!-- Customer Complaint -->
        <div class="info-section">
            <h3>Customer Complaint</h3>
            <div class="notes-box">
                {{ $warrantySubmission->customer_complaint }}
            </div>
        </div>
        
        @if($warrantySubmission->physical_condition_notes)
        <!-- Physical Condition Notes -->
        <div class="info-section">
            <h3>Physical Condition Notes</h3>
            <p>{{ $warrantySubmission->physical_condition_notes }}</p>
        </div>
        @endif
        
        <!-- Service Information -->
        @if($warrantySubmission->service_notes || $warrantySubmission->service_charge)
        <div class="info-section">
            <h3>Service Information</h3>
            @if($warrantySubmission->service_notes)
                <p><strong>Service Notes:</strong></p>
                <div class="notes-box">
                    {{ $warrantySubmission->service_notes }}
                </div>
            @endif
            @if($warrantySubmission->service_charge)
                <p><strong>Service Charge:</strong> ৳{{ number_format($warrantySubmission->service_charge, 2) }}</p>
            @endif
        </div>
        @endif
        
        <!-- Important Notes -->
        <div class="info-section">
            <table class="details-table">
                <tr>
                    <th>Status:</th>
                    <td><strong>{{ ucfirst(str_replace('_', ' ', $warrantySubmission->status)) }}</strong></td>
                </tr>
                @if($warrantySubmission->expected_completion_date)
                <tr>
                    <th>Expected Completion:</th>
                    <td>{{ $warrantySubmission->expected_completion_date->format('d M Y') }}</td>
                </tr>
                @endif
                @if($warrantySubmission->completion_date)
                <tr>
                    <th>Completion Date:</th>
                    <td>{{ $warrantySubmission->completion_date->format('d M Y') }}</td>
                </tr>
                @endif
                @if($warrantySubmission->return_date)
                <tr>
                    <th>Return Date:</th>
                    <td>{{ $warrantySubmission->return_date->format('d M Y') }}</td>
                </tr>
                @endif
            </table>
        </div>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <p><strong>Customer Signature</strong></p>
                <p style="margin-top: 30px;">_________________________</p>
            </div>
            <div class="signature-box">
                <p><strong>Authorized Signature</strong></p>
                <p style="margin-top: 30px;">_________________________</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Please keep this memo safe. You will need it to collect your product.</p>
            <p style="margin-top: 5px;">This is a computer-generated warranty memo.</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Print Memo</button>
        <a href="{{ route('warranty-submissions.show', $warrantySubmission) }}" class="btn btn-secondary">Back</a>
    </div>
</body>
</html>

