<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $sale->invoice_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.print-styles')
</head>
<body>
    <div class="print-container">
        <div class="print-actions no-print">
            <button onclick="window.print()" class="btn btn-primary">Print</button>
            <a href="{{ route('sales.show', $sale) }}" class="btn">Back</a>
        </div>

        @php
            $company = \App\Http\Controllers\CompanyInfoController::getCompanySettings();
        @endphp
        @include('partials.print-header-dynamic', [
            'documentTitle' => 'INVOICE',
            'documentNumber' => $sale->invoice_number,
            'documentDate' => $sale->sale_date->format('F d, Y'),
        ])

        <div class="bill-to-section">
            <div class="bill-to-label">Bill To</div>
            <div class="bill-to-name">{{ $sale->customer ? $sale->customer->name : ($sale->customer_name ?? 'Walk-in Customer') }}</div>
            <div class="bill-to-details">
                @if($sale->customer_phone){{ $sale->customer_phone }}<br>@endif
                @if($sale->customer_address){{ $sale->customer_address }}@endif
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <div>
                <span class="bill-to-label">Payment</span>
                <div style="margin-top: 8px;">
                    <span class="status-badge status-{{ $sale->payment_status === 'paid' ? 'paid' : ($sale->payment_status === 'partial' ? 'partial' : 'unpaid') }}">{{ ucfirst($sale->payment_status) }}</span>
                </div>
                <div style="font-size: 9pt; color: #555; margin-top: 6px;">
                    Paid: ৳{{ number_format($sale->paid_amount, 2) }} &nbsp;|&nbsp; Due: ৳{{ number_format($sale->due_amount, 2) }}
                </div>
            </div>
        </div>

        <table class="print-table">
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 28%;">Product</th>
                    <th style="width: 12%;">Barcode</th>
                    <th style="width: 8%;">Qty</th>
                    <th style="width: 11%;">Unit Price</th>
                    <th style="width: 9%;">Discount</th>
                    <th style="width: 10%;">Warranty</th>
                    <th class="text-end" style="width: 18%;">Subtotal</th>
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
                        <td class="text-end">৳{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-wrap">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">৳{{ number_format($sale->subtotal, 2) }}</td>
                </tr>
                @if($sale->tax_amount > 0)
                <tr>
                    <td class="label">Tax</td>
                    <td class="value">৳{{ number_format($sale->tax_amount, 2) }}</td>
                </tr>
                @endif
                @if($sale->discount_amount > 0)
                <tr>
                    <td class="label">Discount</td>
                    <td class="value">(-) ৳{{ number_format($sale->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label">Total Amount</td>
                    <td class="value total-highlight">৳{{ number_format($sale->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($sale->notes)
        <div class="print-terms" style="margin-top: 0;"><strong>Notes:</strong> {{ $sale->notes }}</div>
        @endif

        @include('partials.print-footer-dynamic')
    </div>
</body>
</html>
