<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $purchase->po_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.print-styles')
</head>
<body>
    <div class="print-container">
        <div class="print-actions no-print">
            <button onclick="window.print()" class="btn btn-primary">Print</button>
            <a href="{{ route('purchases.show', $purchase) }}" class="btn">Back</a>
        </div>

        @php
            $company = \App\Http\Controllers\CompanyInfoController::getCompanySettings();
        @endphp
        @include('partials.print-header-dynamic', [
            'documentTitle' => 'PURCHASE ORDER',
            'documentNumber' => $purchase->po_number,
            'documentDate' => $purchase->order_date->format('F d, Y'),
            'documentSubline' => $purchase->expected_delivery_date ? 'Expected delivery: ' . $purchase->expected_delivery_date->format('F d, Y') : null,
        ])

        <div class="bill-to-section">
            <div class="bill-to-label">Supplier</div>
            <div class="bill-to-name">{{ $purchase->supplier->name }}</div>
            <div class="bill-to-details">
                @if($purchase->supplier->company_name){{ $purchase->supplier->company_name }}<br>@endif
                @if($purchase->supplier->email)Email: {{ $purchase->supplier->email }}@endif
                @if($purchase->supplier->email && $purchase->supplier->phone) &nbsp;|&nbsp; @endif
                @if($purchase->supplier->phone)Phone: {{ $purchase->supplier->phone }}@endif
                @if($purchase->supplier->address)<br>{{ $purchase->supplier->address }}@endif
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <div>
                <span class="bill-to-label">Order & Payment</span>
                <div style="margin-top: 8px;">
                    <span class="status-badge status-{{ $purchase->payment_status === 'paid' ? 'paid' : ($purchase->payment_status === 'partial' ? 'partial' : 'unpaid') }}">{{ ucfirst($purchase->payment_status) }}</span>
                    <span style="font-size: 9pt; color: #555;"> &nbsp; Status: {{ ucfirst($purchase->status) }}</span>
                </div>
                <div style="font-size: 9pt; color: #555; margin-top: 6px;">
                    @if($purchase->received_date)Received: {{ $purchase->received_date->format('d M Y') }} &nbsp;|&nbsp; @endif
                    Paid: ৳{{ number_format($purchase->paid_amount, 2) }} &nbsp;|&nbsp; Due: ৳{{ number_format($purchase->due_amount, 2) }}
                </div>
            </div>
        </div>

        <table class="print-table">
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 26%;">Product</th>
                    <th style="width: 12%;">Barcode</th>
                    <th style="width: 10%;">Serial</th>
                    <th style="width: 6%;">Qty</th>
                    <th style="width: 12%;">Cost Price</th>
                    <th style="width: 12%;">Selling Price</th>
                    <th class="text-end" style="width: 18%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td><code>{{ $item->barcode }}</code></td>
                        <td>{{ $item->serial_number ?? 'N/A' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>৳{{ number_format($item->cost_price, 2) }}</td>
                        <td>{{ $item->selling_price ? '৳' . number_format($item->selling_price, 2) : 'N/A' }}</td>
                        <td class="text-end">৳{{ number_format($item->cost_price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-wrap">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">৳{{ number_format($purchase->subtotal, 2) }}</td>
                </tr>
                @if($purchase->tax_amount > 0)
                <tr>
                    <td class="label">Tax</td>
                    <td class="value">৳{{ number_format($purchase->tax_amount, 2) }}</td>
                </tr>
                @endif
                @if($purchase->discount_amount > 0)
                <tr>
                    <td class="label">Discount</td>
                    <td class="value">(-) ৳{{ number_format($purchase->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label">Total Amount</td>
                    <td class="value total-highlight">৳{{ number_format($purchase->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($purchase->notes)
        <div class="print-terms" style="margin-top: 0;"><strong>Notes:</strong> {{ $purchase->notes }}</div>
        @endif
        @if($purchase->internal_notes)
        <div class="print-terms" style="margin-top: 6px;"><strong>Internal notes:</strong> {{ $purchase->internal_notes }}</div>
        @endif

        @include('partials.print-footer-dynamic')
    </div>
</body>
</html>
