{{-- Shared print styles: use in sales, purchases, services, warranty memos. --}}
<style>
@page { margin: 0.7cm; size: A4; }
* { margin: 0; padding: 0; box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
body {
    font-family: 'DM Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
    font-size: 9.5pt;
    line-height: 1.5;
    color: #333;
    background: white;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}
.print-container { max-width: 210mm; margin: 0 auto; padding: 0; background: white; }

.print-header { margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-start; }
.print-header .header-left { width: 60%; }
.print-header .header-right { width: 40%; text-align: right; }
.print-header .company-name { font-size: 24pt; font-weight: 800; color: #16a34a; text-transform: uppercase; letter-spacing: -0.5px; margin-bottom: 5px; }
.print-header .company-tagline { font-size: 9pt; color: #15803d; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
.print-header .company-address { font-size: 9pt; color: #555; line-height: 1.5; }
.print-header .doc-title { font-size: 32pt; font-weight: 700; color: #16a34a; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; }
.print-header .doc-number-box { background-color: #16a34a; color: white; padding: 8px 15px; display: inline-block; font-size: 9pt; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.print-header .doc-meta { margin-top: 5px; font-size: 9pt; color: #555; }

.bill-to-section { margin-bottom: 30px; }
.bill-to-label { background-color: #16a34a; color: white; padding: 5px 10px; font-size: 9pt; font-weight: 700; display: inline-block; margin-bottom: 8px; text-transform: uppercase; }
.bill-to-name { font-size: 12pt; font-weight: 700; color: #111; margin-bottom: 4px; }
.bill-to-details { font-size: 9pt; color: #555; line-height: 1.5; }

.print-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
.print-table th { background-color: #16a34a; color: white; padding: 12px 15px; text-align: left; font-weight: 600; font-size: 9pt; text-transform: uppercase; letter-spacing: 0.5px; }
.print-table th.text-end { text-align: right; }
.print-table td { padding: 12px 15px; border-bottom: 1px solid #ecf0f1; color: #111; font-size: 9.5pt; }
.print-table td.text-end { text-align: right; font-weight: 600; }
.print-table tr:nth-child(even) { background-color: #f9fbfb; }

.totals-wrap { display: flex; justify-content: flex-end; margin-bottom: 40px; }
.totals-table { width: 40%; border-collapse: collapse; }
.totals-table td { padding: 8px 0; font-size: 9.5pt; color: #111; }
.totals-table tr.total-row td { padding-top: 12px; border-top: 2px solid #16a34a; font-weight: 800; font-size: 11pt; color: #111; }
.totals-table .label { text-align: left; font-weight: 600; color: #7f8c8d; }
.totals-table .value { text-align: right; }
.totals-table .value.total-highlight { background-color: #16a34a; color: white !important; padding: 10px 15px !important; }
.totals-table tr.total-row .label { padding-left: 15px; }

.print-footer-note { background-color: #16a34a; color: white; padding: 10px 15px; font-size: 9pt; font-weight: 600; text-transform: uppercase; text-align: left; margin-top: 20px; }
.print-terms { font-size: 8pt; color: #7f8c8d; margin-top: 10px; line-height: 1.4; white-space: pre-line; }
.print-signature { text-align: right; margin-top: 40px; }
.print-signature-line { display: inline-block; width: 200px; border-top: 1px solid #16a34a; padding-top: 5px; text-align: center; font-size: 9pt; color: #16a34a; }
.print-generated { margin-top: 30px; text-align: center; font-size: 8pt; color: #999; }

.status-badge { padding: 4px 8px; border-radius: 4px; font-size: 8pt; font-weight: 700; text-transform: uppercase; display: inline-block; }
.status-paid { background-color: #dcfce7; color: #166534; }
.status-unpaid { background-color: #fee2e2; color: #991b1b; }
.status-partial { background-color: #fef9c3; color: #854d0e; }

.print-actions { text-align: center; padding: 15px; background: #f5f5f5; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; }
.print-actions .btn { padding: 10px 20px; margin: 5px; border: 1px solid #000; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block; background: white; color: #000; }
.print-actions .btn:hover { background: #f5f5f5; }
.print-actions .btn-primary { background: #000; color: white; }
.print-actions .btn-primary:hover { background: #333; }

@media print {
    body { background: white; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    .print-container { padding: 0; }
    .no-print { display: none !important; }
    .print-section { page-break-inside: avoid; }
    @page { margin: 0.7cm; }
}
</style>
