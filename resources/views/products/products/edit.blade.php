@extends('layouts.dashboard')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="table-card">
            <div class="table-card-header">
                <h6><i class="fas fa-box me-2"></i>Edit Product</h6>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
            
            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="Auto-generated if empty">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="brand_id" class="form-label">Brand</label>
                            <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id" name="brand_id" onchange="loadModels(this.value)">
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="product_model_id" class="form-label">Model</label>
                            <select class="form-select @error('product_model_id') is-invalid @enderror" id="product_model_id" name="product_model_id">
                                <option value="">Select Model</option>
                            </select>
                            @error('product_model_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="specifications" class="form-label">Specifications</label>
                            <textarea class="form-control @error('specifications') is-invalid @enderror" id="specifications" name="specifications" rows="3" placeholder="Key specifications...">{{ old('specifications', $product->specifications) }}</textarea>
                            @error('specifications')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Pricing -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-dollar-sign me-2"></i>Pricing</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cost_price" class="form-label">Cost Price (BDT) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required>
                            @error('cost_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="selling_price" class="form-label">Selling Price (BDT) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required>
                            @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="discount_price" class="form-label">Discount Price (BDT)</label>
                            <input type="number" step="0.01" class="form-control @error('discount_price') is-invalid @enderror" id="discount_price" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}">
                            @error('discount_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Additional Settings -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-cog me-2"></i>Additional Settings</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="warranty_period" class="form-label">Warranty Period (Days)</label>
                            <input type="number" step="1" min="0" class="form-control @error('warranty_period') is-invalid @enderror" id="warranty_period" name="warranty_period" value="{{ old('warranty_period', $product->warranty_period) }}" placeholder="e.g., 365 for 1 year">
                            @error('warranty_period')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Enter warranty period in days. Examples: 30 (1 month), 90 (3 months), 365 (1 year), 730 (2 years)
                                @if($product->warranty_period)
                                    <br>Current: {{ $product->warranty_period }} days ({{ number_format($product->warranty_period / 30, 1) }} months)
                                @endif
                            </small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active Product</label>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Featured Product</label>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="notes" class="form-label">Internal Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', $product->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadModels(brandId) {
    const modelSelect = document.getElementById('product_model_id');
    
    if (!brandId) {
        modelSelect.innerHTML = '<option value="">Select Model</option>';
        return Promise.resolve();
    }
    
    modelSelect.innerHTML = '<option value="">Loading...</option>';
    modelSelect.disabled = true;
    
    return fetch(`{{ route('products.models-by-brand') }}?brand_id=${brandId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                // Try to get error message from response
                return response.json().then(err => {
                    throw new Error(err.message || `HTTP error! status: ${response.status}`);
                }).catch(() => {
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            // Check if response is an array (success) or has error
            if (Array.isArray(data)) {
                modelSelect.innerHTML = '<option value="">Select Model</option>';
                if (data.length > 0) {
                    data.forEach(model => {
                        const option = document.createElement('option');
                        option.value = model.id;
                        option.textContent = model.name;
                        modelSelect.appendChild(option);
                    });
                } else {
                    modelSelect.innerHTML = '<option value="">No models available</option>';
                }
            } else if (data.error) {
                throw new Error(data.message || data.error);
            } else {
                modelSelect.innerHTML = '<option value="">No models available</option>';
            }
            modelSelect.disabled = false;
            return data;
        })
        .catch(error => {
            console.error('Error loading models:', error);
            modelSelect.innerHTML = '<option value="">Error: ' + error.message + '</option>';
            modelSelect.disabled = false;
            throw error;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    // Load models if brand is already selected
    const brandId = document.getElementById('brand_id').value;
    if (brandId) {
        loadModels(brandId).then(() => {
            // Select the current model after loading
            const currentModelId = {{ $product->product_model_id ?? 'null' }};
            if (currentModelId) {
                document.getElementById('product_model_id').value = currentModelId;
            }
        });
    }
});
</script>
@endpush
@endsection

