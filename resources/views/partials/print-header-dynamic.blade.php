@php
    $company = $company ?? \App\Http\Controllers\CompanyInfoController::getCompanySettings();
    $showLogo = function_exists('settings') && settings('pdf_design.show_logo');
    $logoUrl = function_exists('settings') ? trim((string) settings('pdf_design.logo_url', '')) : '';
    $showCompanyName = !function_exists('settings') || settings('pdf_design.show_company_name');
    $showTagline = !function_exists('settings') || settings('pdf_design.show_tagline');
    $showAddress = !function_exists('settings') || settings('pdf_design.show_address');
@endphp
<div class="print-header">
    <div class="header-left">
        <div class="company-info">
            @if($showLogo && $logoUrl !== '')
                @php
                    $logoSrc = str_starts_with($logoUrl, 'http') ? $logoUrl : (str_starts_with($logoUrl, '/') ? asset(ltrim($logoUrl, '/')) : asset('storage/' . $logoUrl));
                @endphp
                <img src="{{ $logoSrc }}" alt="Logo" class="header-logo">
            @endif
            @if($showCompanyName)
                <div class="company-name">{{ $company->company_name ?? 'ERP System' }}</div>
            @endif
            @if($showTagline)
                @if(!empty($company->tagline))
                    <div class="company-tagline">{{ $company->tagline }}</div>
                @elseif(!empty($company->service_center_name) && isset($useServiceCenterName) && $useServiceCenterName)
                    <div class="company-tagline">{{ $company->service_center_name }}</div>
                @endif
            @endif
            @if($showAddress)
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
            @endif
        </div>
    </div>
    <div class="header-right">
        <div class="doc-title">{{ $documentTitle ?? 'DOCUMENT' }}</div>
        <div class="doc-number-box">{{ $documentNumber ?? '' }}</div>
        @if(!empty($documentDate))<div class="doc-meta">Date: {{ $documentDate }}</div>@endif
        @if(!empty($documentSubline))<div class="doc-meta">{{ $documentSubline }}</div>@endif
    </div>
</div>
