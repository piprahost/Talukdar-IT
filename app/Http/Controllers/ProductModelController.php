<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductModelController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view product-models');
        $query = ProductModel::with(['brand'])->withCount('products')->latest();

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->has('brand_id') && $request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $models = $query->paginate(15)->appends($request->query());
        $brands = Brand::active()->ordered()->get();

        return view('products.models.index', compact('models', 'brands'));
    }

    public function create()
    {
        $this->authorizePermission('create product-models');
        $brands = Brand::active()->ordered()->get();
        return view('products.models.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create product-models');
        $validated = $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        ProductModel::create($validated);

        return redirect()->route('product-models.index')
            ->with('success', 'Product model created successfully.');
    }

    public function show(ProductModel $productModel)
    {
        $this->authorizePermission('view product-models');
        $productModel->load(['brand'])->loadCount('products');
        return view('products.models.show', compact('productModel'));
    }

    public function edit(ProductModel $productModel)
    {
        $this->authorizePermission('edit product-models');
        $brands = Brand::active()->ordered()->get();
        return view('products.models.edit', compact('productModel', 'brands'));
    }

    public function update(Request $request, ProductModel $productModel)
    {
        $this->authorizePermission('edit product-models');
        $validated = $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $productModel->update($validated);

        return redirect()->route('product-models.index')
            ->with('success', 'Product model updated successfully.');
    }

    public function destroy(ProductModel $productModel)
    {
        $this->authorizePermission('delete product-models');
        if ($productModel->products()->count() > 0) {
            return redirect()->route('product-models.index')
                ->with('error', 'Cannot delete model with existing products.');
        }

        $productModel->delete();

        return redirect()->route('product-models.index')
            ->with('success', 'Product model deleted successfully.');
    }
}
