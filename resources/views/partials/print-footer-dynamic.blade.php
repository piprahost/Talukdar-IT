@php
    $company = $company ?? \App\Http\Controllers\CompanyInfoController::getCompanySettings();
    $footerLabel = function_exists('settings') ? (settings('pdf_design.footer_label') ?: 'Terms & Conditions') : 'Terms & Conditions';
    $footerNote = function_exists('settings') ? trim((string) settings('pdf_design.footer_note', '')) : '';
    $termsText = $footerNote !== '' ? $footerNote : ($company->terms_and_conditions ?? 'Thank you for your business. Please contact us for any queries.');
    $generatedNote = function_exists('settings') ? (settings('pdf_design.generated_note') ?: 'This is a computer generated document.') : 'This is a computer generated document.';
@endphp
<div class="print-footer-note">{{ $footerLabel }}</div>
<div class="print-terms">{{ $termsText }}</div>
<div class="print-signature">
    <div class="print-signature-line">Authorized Signature</div>
</div>
<div class="print-generated">{{ $generatedNote }}</div>
