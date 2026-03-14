@extends('layouts.dashboard')

@section('title', 'Add New Product')
@section('page-title', 'Add New Product')

@section('content')
<div class="product-form-wrap">
    {{-- Page header --}}
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h5 class="mb-1 fw-bold">Add New Product</h5>
            <p class="text-muted small mb-0">Enter product details, stock limits, and pricing.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
            <button type="submit" form="productForm" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Create Product
            </button>
        </div>
    </div>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf
        <div class="row g-4">
            {{-- Left column --}}
            <div class="col-lg-8">
                {{-- Basic information --}}
                <div class="table-card mb-0">
                    <div class="table-card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-box-open me-2 text-primary"></i>Basic information</h6>
                    </div>
                    <div class="p-4 pt-3">
                        <div class="row g-3">
                            <div class="col-12 col-md-8">
                                <label class="form-label fw-semibold">Product name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name') }}" required placeholder="e.g. Dell XPS 15 Laptop" autofocus>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">SKU</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                       name="sku" value="{{ old('sku') }}" placeholder="Leave blank to auto-generate">
                                @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text small">Auto-generated if left blank</div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Category @if($categoryRequired ?? true)<span class="text-danger">*</span>@endif</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" @if($categoryRequired ?? true) required @endif>
                                    <option value="">Select category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Brand</label>
                                <select class="form-select @error('brand_id') is-invalid @enderror" name="brand_id" id="brand_id" onchange="loadModels(this.value)">
                                    <option value="">Select brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id')==$brand->id?'selected':'' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Model</label>
                                <select class="form-select @error('product_model_id') is-invalid @enderror" name="product_model_id" id="product_model_id">
                                    <option value="">Select model</option>
                                </select>
                                @error('product_model_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Primary barcode @if($requireBarcode ?? false)<span class="text-danger">*</span>@endif</label>
                                <input type="text" class="form-control @error('barcode') is-invalid @enderror" name="barcode" value="{{ old('barcode') }}" placeholder="Scan or enter" @if($requireBarcode ?? false) required @endif>
                                @error('barcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6 col-lg-2">
                                <label class="form-label fw-semibold">Unit</label>
                                <select class="form-select @error('unit') is-invalid @enderror" name="unit">
                                    <option value="pcs"  {{ old('unit','pcs')=='pcs'  ?'selected':'' }}>pcs</option>
                                    <option value="unit" {{ old('unit')=='unit' ?'selected':'' }}>unit</option>
                                    <option value="set"  {{ old('unit')=='set'  ?'selected':'' }}>set</option>
                                    <option value="box"  {{ old('unit')=='box'  ?'selected':'' }}>box</option>
                                    <option value="kg"   {{ old('unit')=='kg'   ?'selected':'' }}>kg</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="2" placeholder="Brief product description">{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Specifications</label>
                                <textarea class="form-control @error('specifications') is-invalid @enderror" name="specifications" rows="2" placeholder="Processor, RAM, storage, display...">{{ old('specifications') }}</textarea>
                                @error('specifications')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stock settings --}}
                <div class="table-card mt-4">
                    <div class="table-card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-warehouse me-2 text-primary"></i>Stock settings</h6>
                    </div>
                    <div class="p-4 pt-3">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold small text-muted">Reorder level</label>
                                <input type="number" min="0" class="form-control form-control-sm @error('reorder_level') is-invalid @enderror"
                                       name="reorder_level" value="{{ old('reorder_level', 5) }}" placeholder="5">
                                @error('reorder_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text small">Alert when stock ≤ this</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold small text-muted">Min stock</label>
                                <input type="number" min="0" class="form-control form-control-sm @error('min_stock') is-invalid @enderror"
                                       name="min_stock" value="{{ old('min_stock', 1) }}" placeholder="1">
                                @error('min_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold small text-muted">Max stock</label>
                                <input type="number" min="0" class="form-control form-control-sm @error('max_stock') is-invalid @enderror"
                                       name="max_stock" value="{{ old('max_stock', 100) }}" placeholder="100">
                                @error('max_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold small text-muted">Warranty (days)</label>
                                <input type="number" min="0" class="form-control form-control-sm @error('warranty_period') is-invalid @enderror"
                                       name="warranty_period" value="{{ old('warranty_period') }}" placeholder="0">
                                @error('warranty_period')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text small">0 = none</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Internal notes</label>
                                <textarea class="form-control form-control-sm @error('notes') is-invalid @enderror" name="notes" rows="2"
                                          placeholder="Private notes...">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right column: Pricing & actions --}}
            <div class="col-lg-4">
                <div class="product-form-sidebar">
                    {{-- Pricing --}}
                    <div class="table-card mb-4">
                        <div class="table-card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-tag me-2 text-primary"></i>Pricing (BDT)</h6>
                        </div>
                        <div class="p-4 pt-3">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Cost price <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="0" class="form-control @error('cost_price') is-invalid @enderror"
                                           name="cost_price" id="cost_price" value="{{ old('cost_price', 0) }}" required oninput="updateMargin()">
                                </div>
                                @error('cost_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Selling price <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="0" class="form-control @error('selling_price') is-invalid @enderror"
                                           name="selling_price" id="selling_price" value="{{ old('selling_price', 0) }}" required oninput="updateMargin()">
                                </div>
                                @error('selling_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted">Discount price</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="0" class="form-control @error('discount_price') is-invalid @enderror"
                                           name="discount_price" value="{{ old('discount_price') }}" placeholder="0">
                                </div>
                                @error('discount_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="p-3 rounded-3 mb-0" style="background:#f8fafc;border:1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">Profit/unit</span>
                                    <strong id="profitAmount">৳0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-muted">Margin</span>
                                    <strong id="marginPercent" class="margin-value">0%</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="table-card mb-4">
                        <div class="table-card-header bg-light border-0 py-3">
                            <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-toggle-on me-2 text-primary"></i>Status</h6>
                        </div>
                        <div class="p-4 pt-3">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Active</label>
                                <div class="small text-muted">Visible in sales & inventory</div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_featured">Featured</label>
                                <div class="small text-muted">Show on dashboard</div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions (sidebar, desktop) --}}
                    <div class="d-none d-lg-grid gap-2">
                        <button type="submit" form="productForm" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Product
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                    {{-- Actions (mobile, below sidebar content) --}}
                    <div class="d-grid gap-2 d-lg-none mt-2">
                        <button type="submit" form="productForm" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Product
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function updateMargin() {
    var cost = parseFloat(document.getElementById('cost_price').value) || 0;
    var selling = parseFloat(document.getElementById('selling_price').value) || 0;
    var profit = selling - cost;
    var margin = selling > 0 ? (profit / selling * 100) : 0;
    document.getElementById('profitAmount').textContent = '৳' + profit.toFixed(2);
    var el = document.getElementById('marginPercent');
    el.textContent = margin.toFixed(1) + '%';
    el.style.color = margin >= 20 ? '#16a34a' : (margin >= 10 ? '#d97706' : '#dc2626');
}
function loadModels(brandId) {
    var sel = document.getElementById('product_model_id');
    if (!brandId) { sel.innerHTML = '<option value="">Select model</option>'; return; }
    sel.innerHTML = '<option value="">Loading...</option>';
    sel.disabled = true;
    fetch('{{ route("products.models-by-brand") }}?brand_id=' + brandId, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            sel.innerHTML = '<option value="">Select model</option>';
            if (Array.isArray(data) && data.length) data.forEach(function(m) { var o = document.createElement('option'); o.value = m.id; o.textContent = m.name; sel.appendChild(o); });
            else sel.innerHTML = '<option value="">No models</option>';
            sel.disabled = false;
        })
        .catch(function() { sel.innerHTML = '<option value="">Error</option>'; sel.disabled = false; });
}
document.addEventListener('DOMContentLoaded', function() {
    updateMargin();
    var brandId = document.getElementById('brand_id').value;
    if (brandId) loadModels(brandId);
});
</script>
@endpush
@endsection
