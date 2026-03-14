{{-- Custom API (cURL) SMS config – improved visual edit --}}
@php
    $v = fn($key, $default = '') => old($key, $values[$key] ?? $default);
    $checked = fn($key, $default = '0') => (($x = $v($key, $default)) && $x !== '0' && $x !== false);
@endphp

<div class="mb-4">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="enabled" value="1" id="setting_enabled" {{ $checked('enabled', $defs['enabled']['default'] ?? '0') ? 'checked' : '' }}>
        <label class="form-check-label fw-semibold" for="setting_enabled">{{ $defs['enabled']['label'] ?? 'Enable SMS' }}</label>
    </div>
    @if(!empty($defs['enabled']['description']))
        <div class="form-text">{{ $defs['enabled']['description'] }}</div>
    @endif
</div>

<div class="card border mb-4" style="border-color: var(--bs-primary) !important;">
    <div class="card-header py-2 d-flex align-items-center">
        <i class="fas fa-code me-2 text-primary"></i>
        <strong>Custom API (HTTP / cURL)</strong>
    </div>
    <div class="card-body">
        <p class="small text-muted mb-3">Configure one HTTP request per SMS. Use placeholders <code>{{phone}}</code>, <code>{{message}}</code>, <code>{{sender_id}}</code> in URL, headers and body.</p>

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">API URL</label>
                <input type="text" class="form-control font-monospace" name="custom_url" id="setting_custom_url"
                       value="{{ $v('custom_url', $defs['custom_url']['default'] ?? '') }}"
                       placeholder="https://api.example.com/sms/send">
                @if(!empty($defs['custom_url']['description']))
                    <div class="form-text">{{ $defs['custom_url']['description'] }}</div>
                @endif
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Method</label>
                <select class="form-select" name="custom_method" id="setting_custom_method">
                    @foreach(($defs['custom_method']['options'] ?? []) as $optVal => $optLabel)
                        <option value="{{ $optVal }}" {{ (string)$v('custom_method', $defs['custom_method']['default'] ?? 'POST') === (string)$optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label fw-semibold">Request headers <span class="text-muted fw-normal">(one per line: <code>Name: value</code>)</span></label>
            <textarea class="form-control font-monospace" name="custom_headers" id="setting_custom_headers" rows="5"
                      placeholder="Content-Type: application/json&#10;Authorization: Bearer YOUR_API_KEY"
                      style="font-size: 0.9rem;">{{ $v('custom_headers', $defs['custom_headers']['default'] ?? '') }}</textarea>
            @if(!empty($defs['custom_headers']['description']))
                <div class="form-text">{{ $defs['custom_headers']['description'] }}</div>
            @endif
        </div>

        <div class="mt-3">
            <label class="form-label fw-semibold">Request body <span class="text-muted fw-normal">(for POST/PUT/PATCH)</span></label>
            <textarea class="form-control font-monospace" name="custom_body" id="setting_custom_body" rows="6"
                      placeholder='{"to": "{{phone}}", "message": "{{message}}"}'
                      style="font-size: 0.9rem;">{{ $v('custom_body', $defs['custom_body']['default'] ?? '') }}</textarea>
            @if(!empty($defs['custom_body']['description']))
                <div class="form-text">{{ $defs['custom_body']['description'] }}</div>
            @endif
        </div>

        <div class="mt-3">
            <label class="form-label fw-semibold">Sender ID (optional)</label>
            <input type="text" class="form-control" name="sender_id" value="{{ $v('sender_id', $defs['sender_id']['default'] ?? '') }}" placeholder="e.g. COMPANY">
            @if(!empty($defs['sender_id']['description']))
                <div class="form-text">{{ $defs['sender_id']['description'] }}</div>
            @endif
        </div>
    </div>
</div>

<div class="border-top pt-3">
    <h6 class="text-muted small text-uppercase mb-2">Test mode</h6>
    <div class="form-check form-switch mb-2">
        <input class="form-check-input" type="checkbox" name="test_mode" value="1" id="setting_test_mode" {{ $checked('test_mode', $defs['test_mode']['default'] ?? '0') ? 'checked' : '' }}>
        <label class="form-check-label" for="setting_test_mode">{{ $defs['test_mode']['label'] ?? 'Test mode' }}</label>
    </div>
    <div class="mb-0">
        <label class="form-label small" for="setting_test_number">{{ $defs['test_number']['label'] ?? 'Test phone number' }}</label>
        <input type="text" class="form-control form-control-sm" name="test_number" id="setting_test_number" value="{{ $v('test_number', $defs['test_number']['default'] ?? '') }}" placeholder="+880...">
        @if(!empty($defs['test_number']['description']))
            <div class="form-text">{{ $defs['test_number']['description'] }}</div>
        @endif
    </div>
</div>
