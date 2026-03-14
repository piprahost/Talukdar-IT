@php
    $company = $company ?? \App\Http\Controllers\CompanyInfoController::getCompanySettings();
@endphp
<div class="print-footer-note">Terms & Conditions</div>
<div class="print-terms">{{ $company->terms_and_conditions ?? 'Thank you for your business. Please contact us for any queries.' }}</div>
<div class="print-signature">
    <div class="print-signature-line">Authorized Signature</div>
</div>
<div class="print-generated">This is a computer generated document.</div>
