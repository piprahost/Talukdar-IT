{{-- Invoice & PDF design – visual edit with live preview, colour pickers, logo upload. --}}
@php
    $v = fn($key, $default = '') => old($key, $values[$key] ?? $defs[$key]['default'] ?? $default);
    $checked = fn($key, $default = '0') => (($x = $v($key, $default)) && $x !== '0' && $x !== false);
    $company = $company ?? null;
    $logoPreviewUrl = '';
    if ($v('logo_url', '') !== '') {
        $lu = $v('logo_url', '');
        $logoPreviewUrl = str_starts_with($lu, 'http') ? $lu : asset('storage/' . ltrim($lu, '/'));
    }
@endphp

{{-- Live preview --}}
<div class="card border border-primary mb-4">
    <div class="card-header py-2 bg-primary bg-opacity-10 d-flex align-items-center">
        <i class="fas fa-eye me-2 text-primary"></i>
        <strong>Live preview</strong>
        <span class="ms-2 small text-muted">(updates as you change options)</span>
    </div>
    <div class="card-body p-3">
        <div id="pdf-preview" class="rounded border bg-white shadow-sm" style="max-width: 100%; overflow: hidden;">
            <div id="pdf-preview-header" class="p-3 d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div class="pdf-preview-left">
                    <div id="pdf-preview-logo-wrap" class="mb-2" style="display: none;">
                        <img id="pdf-preview-logo" src="" alt="Logo" style="max-height: 40px; max-width: 120px; object-fit: contain;">
                    </div>
                    <div id="pdf-preview-company" class="fw-bold text-uppercase" style="font-size: 1rem;">{{ $company->company_name ?? 'Your Company' }}</div>
                    <div id="pdf-preview-tagline" class="small text-uppercase" style="font-size: 0.7rem;">{{ $company->tagline ?? 'Your tagline' }}</div>
                    <div id="pdf-preview-address" class="small text-muted" style="font-size: 0.65rem; line-height: 1.3;">{{ $company->address ?? 'Address' }}<br>{{ $company->city ?? 'City' }}, {{ $company->country ?? 'Country' }}</div>
                </div>
                <div class="text-end">
                    <div id="pdf-preview-doc-title" class="fw-bold text-uppercase" style="font-size: 1rem;">INVOICE</div>
                    <div id="pdf-preview-doc-number" class="text-white px-2 py-1 d-inline-block small">INV-001</div>
                </div>
            </div>
            <div class="px-3 py-2 small border-top" style="border-color: #eee !important;">
                <div id="pdf-preview-footer-label" class="text-white small fw-semibold px-2 py-1 d-inline-block">Terms & Conditions</div>
                <div id="pdf-preview-terms" class="small text-muted mt-1" style="font-size: 0.65rem; white-space: pre-line;"></div>
                <div id="pdf-preview-generated" class="text-center small text-muted mt-2" style="font-size: 0.6rem;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border mb-4" style="border-color: var(--bs-primary) !important;">
            <div class="card-header py-2 d-flex align-items-center">
                <i class="fas fa-palette me-2 text-primary"></i>
                <strong>Colours & typography</strong>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">Used on all downloadable PDFs: sales invoices, purchase orders, service memos, etc.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="setting_primary_color">{{ $defs['primary_color']['label'] ?? 'Primary colour' }}</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" class="form-control form-control-color p-1" id="pdf_primary_swatch" style="width: 3rem; height: 2.5rem;"
                                   value="{{ preg_match('/^#[0-9A-Fa-f]{6}$/', $v('primary_color', '#16a34a')) ? $v('primary_color', '#16a34a') : '#16a34a' }}">
                            <input type="text" class="form-control font-monospace" name="primary_color" id="setting_primary_color"
                                   value="{{ $v('primary_color', '#16a34a') }}" placeholder="#16a34a" maxlength="7">
                        </div>
                        @if(!empty($defs['primary_color']['description']))
                            <div class="form-text">{{ $defs['primary_color']['description'] }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="setting_secondary_color">{{ $defs['secondary_color']['label'] ?? 'Secondary colour' }}</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" class="form-control form-control-color p-1" id="pdf_secondary_swatch" style="width: 3rem; height: 2.5rem;"
                                   value="{{ preg_match('/^#[0-9A-Fa-f]{6}$/', $v('secondary_color', '#15803d')) ? $v('secondary_color', '#15803d') : '#15803d' }}">
                            <input type="text" class="form-control font-monospace" name="secondary_color" id="setting_secondary_color"
                                   value="{{ $v('secondary_color', '#15803d') }}" placeholder="#15803d" maxlength="7">
                        </div>
                        @if(!empty($defs['secondary_color']['description']))
                            <div class="form-text">{{ $defs['secondary_color']['description'] }}</div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="setting_body_font_size_pt">{{ $defs['body_font_size_pt']['label'] ?? 'Body font size (pt)' }}</label>
                        <input type="number" class="form-control" name="body_font_size_pt" id="setting_body_font_size_pt"
                               value="{{ $v('body_font_size_pt', 10) }}" min="8" max="12" step="1">
                        @if(!empty($defs['body_font_size_pt']['description']))
                            <div class="form-text">{{ $defs['body_font_size_pt']['description'] }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card border mb-4">
            <div class="card-header py-2 d-flex align-items-center">
                <i class="fas fa-image me-2 text-muted"></i>
                <strong>Logo & header content</strong>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="show_logo" value="1" id="setting_show_logo" {{ $checked('show_logo', '0') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="setting_show_logo">{{ $defs['show_logo']['label'] ?? 'Show logo on PDFs' }}</label>
                    </div>
                    @if(!empty($defs['show_logo']['description']))
                        <div class="form-text">{{ $defs['show_logo']['description'] }}</div>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload logo</label>
                    <input type="file" class="form-control" name="logo_upload" id="setting_logo_upload" accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml">
                    <div class="form-text">PNG, JPG, GIF, WebP or SVG. Max 2MB. Leave empty to keep current logo.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="setting_logo_url">Or logo URL / path</label>
                    <input type="text" class="form-control" name="logo_url" id="setting_logo_url" value="{{ $v('logo_url', '') }}"
                           placeholder="https://... or /images/logo.png">
                    @if(!empty($defs['logo_url']['description']))
                        <div class="form-text">{{ $defs['logo_url']['description'] }}</div>
                    @endif
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="show_company_name" value="1" id="setting_show_company_name" {{ $checked('show_company_name', '1') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="setting_show_company_name">{{ $defs['show_company_name']['label'] ?? 'Show company name' }}</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="show_tagline" value="1" id="setting_show_tagline" {{ $checked('show_tagline', '1') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="setting_show_tagline">{{ $defs['show_tagline']['label'] ?? 'Show tagline' }}</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="show_address" value="1" id="setting_show_address" {{ $checked('show_address', '1') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="setting_show_address">{{ $defs['show_address']['label'] ?? 'Show address & contact' }}</label>
                </div>
                @if(!empty($defs['show_address']['description']))
                    <div class="form-text">{{ $defs['show_address']['description'] }}</div>
                @endif
            </div>
        </div>

        <div class="card border mb-4">
            <div class="card-header py-2 d-flex align-items-center">
                <i class="fas fa-align-left me-2 text-muted"></i>
                <strong>Footer & terms</strong>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="setting_footer_label">{{ $defs['footer_label']['label'] ?? 'Footer section label' }}</label>
                    <input type="text" class="form-control" name="footer_label" id="setting_footer_label" value="{{ $v('footer_label', 'Terms & Conditions') }}">
                    @if(!empty($defs['footer_label']['description']))
                        <div class="form-text">{{ $defs['footer_label']['description'] }}</div>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="setting_footer_note">{{ $defs['footer_note']['label'] ?? 'Footer note / terms' }}</label>
                    <textarea class="form-control" name="footer_note" id="setting_footer_note" rows="4" placeholder="Leave blank to use Company Info terms">{{ $v('footer_note', '') }}</textarea>
                    @if(!empty($defs['footer_note']['description']))
                        <div class="form-text">{{ $defs['footer_note']['description'] }}</div>
                    @endif
                </div>
                <div>
                    <label class="form-label fw-semibold" for="setting_generated_note">{{ $defs['generated_note']['label'] ?? 'Generated document note' }}</label>
                    <input type="text" class="form-control" name="generated_note" id="setting_generated_note" value="{{ $v('generated_note', 'This is a computer generated document.') }}">
                    @if(!empty($defs['generated_note']['description']))
                        <div class="form-text">{{ $defs['generated_note']['description'] }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var primarySwatch = document.getElementById('pdf_primary_swatch');
    var primaryInput = document.getElementById('setting_primary_color');
    var secondarySwatch = document.getElementById('pdf_secondary_swatch');
    var secondaryInput = document.getElementById('setting_secondary_color');
    function syncColor(swatch, input) {
        if (!swatch || !input) return;
        function toInput() { input.value = swatch.value; updatePreview(); }
        function toSwatch() {
            var v = (input.value || '').trim();
            if (/^#[0-9A-Fa-f]{6}$/.test(v)) swatch.value = v;
            updatePreview();
        }
        swatch.addEventListener('input', toInput);
        input.addEventListener('input', toSwatch);
        input.addEventListener('change', toSwatch);
    }
    syncColor(primarySwatch, primaryInput);
    syncColor(secondarySwatch, secondaryInput);

    var logoPreviewUrl = @json($logoPreviewUrl);
    function getPrimary() { return (primaryInput && primaryInput.value.trim()) ? primaryInput.value.trim() : '#16a34a'; }
    function getSecondary() { return (secondaryInput && secondaryInput.value.trim()) ? secondaryInput.value.trim() : '#15803d'; }
    function updatePreview() {
        var primary = getPrimary();
        var secondary = getSecondary();
        if (!/^#[0-9A-Fa-f]{6}$/.test(primary)) primary = '#16a34a';
        if (!/^#[0-9A-Fa-f]{6}$/.test(secondary)) secondary = '#15803d';

        var header = document.getElementById('pdf-preview-header');
        var docTitle = document.getElementById('pdf-preview-doc-title');
        var docNumber = document.getElementById('pdf-preview-doc-number');
        var footerLabel = document.getElementById('pdf-preview-footer-label');
        if (header) header.style.setProperty('--pdf-preview-primary', primary);
        if (docTitle) docTitle.style.color = primary;
        if (docNumber) { docNumber.style.backgroundColor = primary; docNumber.style.color = 'white'; }
        if (footerLabel) { footerLabel.style.backgroundColor = primary; footerLabel.style.color = 'white'; }

        var tagline = document.getElementById('pdf-preview-tagline');
        if (tagline) tagline.style.color = secondary;

        var showLogo = document.getElementById('setting_show_logo');
        var logoWrap = document.getElementById('pdf-preview-logo-wrap');
        var logoImg = document.getElementById('pdf-preview-logo');
        if (logoWrap) logoWrap.style.display = (showLogo && showLogo.checked) ? 'block' : 'none';
        if (showLogo && showLogo.checked && logoImg) {
            var urlInput = document.getElementById('setting_logo_url');
            if (logoImg.dataset.objectUrl) logoImg.src = logoImg.dataset.objectUrl;
            else if (urlInput && urlInput.value.trim()) {
                var v = urlInput.value.trim();
                logoImg.src = v.startsWith('http') ? v : (v.startsWith('/') ? '{{ url("/") }}' + v : '{{ url("/") }}/storage/' + v);
            } else if (logoPreviewUrl) logoImg.src = logoPreviewUrl;
        }

        var showCompany = document.getElementById('setting_show_company_name');
        var showTagline = document.getElementById('setting_show_tagline');
        var showAddress = document.getElementById('setting_show_address');
        var elCompany = document.getElementById('pdf-preview-company');
        var elTagline = document.getElementById('pdf-preview-tagline');
        var elAddress = document.getElementById('pdf-preview-address');
        if (elCompany) elCompany.style.display = (showCompany && showCompany.checked) ? 'block' : 'none';
        if (elTagline) elTagline.style.display = (showTagline && showTagline.checked) ? 'block' : 'none';
        if (elAddress) elAddress.style.display = (showAddress && showAddress.checked) ? 'block' : 'none';

        var footerLabelInput = document.getElementById('setting_footer_label');
        var footerNoteInput = document.getElementById('setting_footer_note');
        var generatedInput = document.getElementById('setting_generated_note');
        var elTerms = document.getElementById('pdf-preview-terms');
        var elGenerated = document.getElementById('pdf-preview-generated');
        if (footerLabelInput && footerLabel) footerLabel.textContent = footerLabelInput.value || 'Terms & Conditions';
        if (footerNoteInput && elTerms) elTerms.textContent = footerNoteInput.value || 'Your terms text here.';
        if (generatedInput && elGenerated) elGenerated.textContent = generatedInput.value || 'This is a computer generated document.';
    }

    ['setting_show_logo','setting_show_company_name','setting_show_tagline','setting_show_address','setting_footer_label','setting_footer_note','setting_generated_note','setting_logo_url'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('input', updatePreview);
        if (el) el.addEventListener('change', updatePreview);
    });
    document.getElementById('setting_logo_upload').addEventListener('change', function(e) {
        var file = e.target && e.target.files[0];
        var img = document.getElementById('pdf-preview-logo');
        if (img && img.dataset.objectUrl) URL.revokeObjectURL(img.dataset.objectUrl);
        if (file && img) {
            var url = URL.createObjectURL(file);
            img.dataset.objectUrl = url;
            img.src = url;
            document.getElementById('pdf-preview-logo-wrap').style.display = document.getElementById('setting_show_logo').checked ? 'block' : 'none';
        }
        updatePreview();
    });

    document.querySelectorAll('#setting_show_logo, #setting_show_company_name, #setting_show_tagline, #setting_show_address').forEach(function(el) {
        if (el) el.addEventListener('change', updatePreview);
    });

    updatePreview();
})();
</script>
