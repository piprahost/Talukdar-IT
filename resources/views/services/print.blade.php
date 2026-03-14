<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Order - {{ $service->service_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.print-styles')
</head>
<body>
    <div class="print-container">
        <div class="print-actions no-print">
            <button onclick="window.print()" class="btn btn-primary">Print</button>
            <a href="{{ route('services.show', $service) }}" class="btn">Back</a>
        </div>

        @php
            $company = \App\Http\Controllers\CompanyInfoController::getCompanySettings();
        @endphp
        @include('partials.print-header-dynamic', [
            'documentTitle' => 'SERVICE MEMO',
            'documentNumber' => $service->service_number,
            'documentDate' => $service->receive_date->format('F d, Y'),
            'useServiceCenterName' => true,
        ])

        <div class="bill-to-section">
            <div class="bill-to-label">Bill To</div>
            <div class="bill-to-name">{{ $service->customer_name }}</div>
            <div class="bill-to-details">
                {{ $service->customer_phone }}
                @if($service->customer_address)<br>{{ $service->customer_address }}@endif
            </div>
        </div>

        <table class="print-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Item Description</th>
                    <th style="width: 25%;">Problem / Notes</th>
                    <th style="width: 15%;">Status</th>
                    <th class="text-end" style="width: 20%;">Cost</th>
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
                        <span class="status-badge @if($service->status == 'completed' || $service->status == 'delivered') status-paid @elseif($service->status == 'cancelled') status-unpaid @else status-partial @endif">
                            {{ ucfirst(str_replace('_', ' ', $service->status)) }}
                        </span>
                    </td>
                    <td class="text-end">৳{{ number_format($service->service_cost, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals-wrap">
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

        @include('partials.print-footer-dynamic')
    </div>
</body>
</html>
