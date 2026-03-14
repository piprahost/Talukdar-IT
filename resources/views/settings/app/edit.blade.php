@extends('layouts.dashboard')

@section('title', 'App Settings – ' . ($categories[$currentCategory]['label'] ?? $currentCategory))
@section('page-title', 'App Settings')

@section('content')
<div class="row g-4">
    {{-- Category nav --}}
    <div class="col-lg-3">
        <div class="table-card">
            <div class="table-card-header py-2">
                <h6 class="mb-0 small text-uppercase text-muted">Categories</h6>
            </div>
            <div class="list-group list-group-flush rounded-0">
                @foreach($categories as $slug => $cat)
                <a href="{{ route('settings.app.edit', ['category' => $slug]) }}"
                   class="list-group-item list-group-item-action d-flex align-items-center {{ $slug === $currentCategory ? 'active' : '' }}">
                    <i class="{{ $cat['icon'] }} me-2 fa-fw"></i>
                    <span>{{ $cat['label'] }}</span>
                </a>
                @endforeach
            </div>
            <div class="p-2 border-top">
                <a href="{{ route('company-info.edit') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="fas fa-building me-1"></i>Company Info
                </a>
            </div>
            <div class="p-2 border-top">
                <h6 class="mb-2 small text-uppercase text-muted">Tools</h6>
                <div class="d-flex flex-column gap-2">
                    <form action="{{ route('settings.clear-cache') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm w-100 text-start">
                            <i class="fas fa-broom me-1"></i>Clear cache
                        </button>
                    </form>
                    <form action="{{ route('settings.recalculate-totals') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm w-100 text-start">
                            <i class="fas fa-calculator me-1"></i>Re-calculate totals
                        </button>
                    </form>
                </div>
                <p class="form-text small mb-0 mt-1">Clear cache: app, config, views. Re-calculate: sales & purchase totals from line items.</p>
            </div>
        </div>
    </div>

    {{-- Form for current category --}}
    <div class="col-lg-9">
        <form action="{{ route('settings.app.update', ['category' => $currentCategory]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="table-card">
                <div class="table-card-header d-flex align-items-center">
                    <h6 class="mb-0">
                        <i class="{{ $categories[$currentCategory]['icon'] ?? 'fas fa-cog' }} me-2"></i>
                        {{ $categories[$currentCategory]['label'] ?? $currentCategory }}
                    </h6>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        @foreach($defs as $key => $def)
                        @php
                            $type = $def['type'] ?? 'text';
                            $value = $values[$key] ?? ($def['default'] ?? '');
                            $inputName = $key;
                        @endphp
                        <div class="col-12">
                            @if($type === 'boolean')
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="{{ $inputName }}" value="1"
                                           id="setting_{{ $key }}" {{ $value ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="setting_{{ $key }}">{{ $def['label'] }}</label>
                                </div>
                            @else
                                <label class="form-label fw-semibold" for="setting_{{ $key }}">{{ $def['label'] }}</label>
                                @if(isset($def['options']) && is_array($def['options']))
                                    <select class="form-select" id="setting_{{ $key }}" name="{{ $inputName }}">
                                        @foreach($def['options'] as $optVal => $optLabel)
                                            <option value="{{ $optVal }}" {{ (string)old($key, $value) === (string)$optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
                                        @endforeach
                                    </select>
                                @elseif($type === 'integer')
                                    <input type="number" class="form-control" id="setting_{{ $key }}" name="{{ $inputName }}"
                                           value="{{ old($key, $value) }}" min="0" step="1">
                                @else
                                    @php $inputType = $def['input_type'] ?? 'text'; @endphp
                                    <input type="{{ $inputType }}" class="form-control" id="setting_{{ $key }}" name="{{ $inputName }}"
                                           value="{{ old($key, $value) }}" placeholder="{{ $def['default'] ?? '' }}" autocomplete="off">
                                @endif
                            @endif
                            @if(!empty($def['description']))
                                <div class="form-text">{{ $def['description'] }}</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="table-card-header bg-light d-flex justify-content-end gap-2 py-2">
                    <a href="{{ route('settings.app.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
