@extends('layouts.dashboard')

@section('title', 'Add New Product')
@section('page-title', 'Add New Product')

@section('content')
<div class="row g-3">
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf

        {{-- ── Left Column ── --}}
        <div class="col-md-8">

            {{-- Basic Info --}}
            <div class="table-card mb-3">
                <div class="table-card-header">
                    <h6><i class="fas fa-box me-2"></i>Product Information</h6>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" required placeholder="e.g. Dell Laptop XPS 15" autofocus>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">SKU
                                <small class="text-muted">(auto-generated)</small>
                            </label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                   name="sku" value="{{ old('sku') }}" placeholder="Auto-generated if blank">
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                                <option value="">— Select Category —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Brand</label>
                            <select class="form-select @error('brand_id') is-invalid @enderror" name="brand_id" id="brand_id" onchange="loadModels(this.value)">
                                <option value="">— Select Brand —</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id')==$brand->id?'selected':'' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Model</label>
                            <select class="form-select @error('product_model_id') is-invalid @enderror" name="product_model_id" id="product_model_id">
                                <option value="">— Select Model —</option>
                            </select>
                            @error('product_model_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Unit</label>
                            <select class="form-select @error('unit') is-invalid @enderror" name="unit">
                                <option value="pcs"  {{ old('unit','pcs')=='pcs'  ?'selected':'' }}>Piece (pcs)</option>
                                <option value="unit" {{ old('unit')=='unit' ?'selected':'' }}>Unit</option>
                                <option value="set"  {{ old('unit')=='set'  ?'selected':'' }}>Set</option>
                                <option value="box"  {{ old('unit')=='box'  ?'selected':'' }}>Box</option>
                                <option value="kg"   {{ old('unit')=='kg'   ?'selected':'' }}>KG</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3" placeholder="Product description...">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Specifications</label>
                            <textarea class="form-control @error('specifications') is-invalid @enderror" name="specifications" rows="3" placeholder="Processor, RAM, Storage, Display, etc...">{{ old('specifications') }}</textarea>
                            @error('specifications')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stock Settings --}}
            <div class="table-card">
                <div class="table-card-header">
                    <h6><i class="fas fa-warehouse me-2"></i>Stock Settings</h6>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Reorder Level</label>
                            <input type="number" min="0" class="form-control @error('reorder_level') is-invalid @enderror"
                                   name="reorder_level" value="{{ old('reorder_level', 5) }}">
                            @error('reorder_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Alert when stock hits this level</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Minimum Stock</label>
                            <input type="number" min="0" class="form-control @error('min_stock') is-invalid @enderror"
                                   name="min_stock" value="{{ old('min_stock', 1) }}">
                            @error('min_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Critical low stock level</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Max Stock</label>
                            <input type="number" min="0" class="form-control @error('max_stock') is-invalid @enderror"
                                   name="max_stock" value="{{ old('max_stock', 100) }}">
                            @error('max_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Maximum stock to hold</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Warranty (Days)</label>
                            <input type="number" min="0" class="form-control @error('warranty_period') is-invalid @enderror"
                                   name="warranty_period" value="{{ old('warranty_period') }}" placeholder="e.g. 365">
                            @error('warranty_period')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">0 = No warranty</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Internal Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="2"
                                      placeholder="Private notes about this product...">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Right Column ── --}}
        <div class="col-md-4">

            {{-- Pricing Card --}}
            <div class="table-card mb-3">
                <div class="table-card-header">
                    <h6><i class="fas fa-tag me-2"></i>Pricing (BDT)</h6>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="form-label">Cost Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">৳</span>
                            <input type="number" step="0.01" min="0"
                                   class="form-control @error('cost_price') is-invalid @enderror"
                                   name="cost_price" id="cost_price" value="{{ old('cost_price', 0) }}" required
                                   oninput="updateMargin()">
                        </div>
                        @error('cost_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">৳</span>
                            <input type="number" step="0.01" min="0"
                                   class="form-control @error('selling_price') is-invalid @enderror"
                                   name="selling_price" id="selling_price" value="{{ old('selling_price', 0) }}" required
                                   oninput="updateMargin()">
                        </div>
                        @error('selling_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Price
                            <small class="text-muted">(optional)</small>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">৳</span>
                            <input type="number" step="0.01" min="0"
                                   class="form-control @error('discount_price') is-invalid @enderror"
                                   name="discount_price" value="{{ old('discount_price') }}" placeholder="0.00">
                        </div>
                        @error('discount_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Profit margin preview --}}
                    <div id="marginPreview" class="p-3 rounded" style="background:#f9fafb;border:1px solid #e5e7eb;">
                        <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                            <span class="text-muted">Profit per unit</span>
                            <strong id="profitAmount">৳0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between" style="font-size:13px;">
                            <span class="text-muted">Profit margin</span>
                            <strong id="marginPercent" style="color:#16a34a;">0%</strong>
                        </div>
                        <div class="mt-2" style="font-size:11px;color:#9ca3af;">
                            <i class="fas fa-info-circle me-1"></i>Based on selling price
                        </div>
                    </div>
                </div>
            </div>

            {{-- Settings --}}
            <div class="table-card mb-3">
                <div class="table-card-header">
                    <h6><i class="fas fa-cog me-2"></i>Settings</h6>
                </div>
                <div class="p-4">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">Active Product</label>
                        <div style="font-size:12px;color:#9ca3af;">Visible in sales & inventory</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                               {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_featured">⭐ Featured Product</label>
                        <div style="font-size:12px;color:#9ca3af;">Show as featured in dashboard</div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Create Product
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
function updateMargin() {
    const cost    = parseFloat(document.getElementById('cost_price').value) || 0;
    const selling = parseFloat(document.getElementById('selling_price').value) || 0;
    const profit  = selling - cost;
    const margin  = selling > 0 ? (profit / selling * 100) : 0;

    document.getElementById('profitAmount').textContent = '৳' + profit.toFixed(2);
    const marginEl = document.getElementById('marginPercent');
    marginEl.textContent = margin.toFixed(1) + '%';
    marginEl.style.color = margin >= 20 ? '#16a34a' : (margin >= 10 ? '#f59e0b' : '#ef4444');
}

function loadModels(brandId) {
    const modelSelect = document.getElementById('product_model_id');
    if (!brandId) {
        modelSelect.innerHTML = '<option value="">— Select Model —</option>';
        return;
    }
    modelSelect.innerHTML = '<option value="">Loading...</option>';
    modelSelect.disabled = true;

    fetch(`{{ route('products.models-by-brand') }}?brand_id=${brandId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(data => {
        modelSelect.innerHTML = '<option value="">— Select Model —</option>';
        if (Array.isArray(data) && data.length) {
            data.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m.id;
                opt.textContent = m.name;
                modelSelect.appendChild(opt);
            });
        } else {
            modelSelect.innerHTML = '<option value="">No models for this brand</option>';
        }
        modelSelect.disabled = false;
    })
    .catch(() => {
        modelSelect.innerHTML = '<option value="">Error loading models</option>';
        modelSelect.disabled = false;
    });
}

document.addEventListener('DOMContentLoaded', function () {
    updateMargin();
    const brandId = document.getElementById('brand_id').value;
    if (brandId) loadModels(brandId);
});
</script>
@endpush
@endsection
