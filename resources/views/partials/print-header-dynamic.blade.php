@php
    $company = $company ?? \App\Http\Controllers\CompanyInfoController::getCompanySettings();
@endphp
<div class="print-header">
    <div class="header-left">
        <div class="company-info">
            <div class="company-name">{{ $company->company_name ?? 'ERP System' }}</div>
            @if(!empty($company->tagline))
                <div class="company-tagline">{{ $company->tagline }}</div>
            @elseif(!empty($company->service_center_name) && isset($useServiceCenterName) && $useServiceCenterName)
                <div class="company-tagline">{{ $company->service_center_name }}</div>
            @endif
            <div class="company-address">
                @if(!empty($company->address)){{ $company->address }}<br>@endif
                {{ $company->city ?? 'Dhaka' }}{{ !empty($company->country) ? ', ' . $company->country : ', Bangladesh' }}
                @if(!empty($company->phone) || !empty($company->email))
                    <br>
                    @if(!empty($company->phone))Phone: {{ $company->phone }}@endif
                    @if(!empty($company->phone) && !empty($company->email)) | @endif
                    @if(!empty($company->email))Email: {{ $company->email }}@endif
                @endif
                @if(!empty($company->website))<br>Web: {{ $company->website }}@endif
            </div>
        </div>
    </div>
    <div class="header-right">
        <div class="doc-title">{{ $documentTitle ?? 'DOCUMENT' }}</div>
        <div class="doc-number-box">{{ $documentNumber ?? '' }}</div>
        @if(!empty($documentDate))<div class="doc-meta">Date: {{ $documentDate }}</div>@endif
        @if(!empty($documentSubline))<div class="doc-meta">{{ $documentSubline }}</div>@endif
    </div>
</div>
