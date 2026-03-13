<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductModel;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view products');
        $query = Product::with(['category', 'brand', 'productModel'])->latest();

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('brand_id') && $request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'out_of_stock') {
                $query->where('status', 'out_of_stock');
            } elseif ($request->status === 'low_stock') {
                $query->lowStock();
            }
        }

        $products = $query->paginate(20)->appends($request->query());
        $categories = Category::active()->ordered()->get();
        $brands = Brand::active()->ordered()->get();

        return view('products.products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $this->authorizePermission('create products');
        $categories = Category::active()->ordered()->get();
        $brands = Brand::active()->ordered()->get();
        return view('products.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create products');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'product_model_id' => ['nullable', 'exists:product_models,id'],
            'description' => ['nullable', 'string'],
            'specifications' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:50'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'warranty_period' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['created_by'] = auth()->id();

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $this->authorizePermission('view products');
        $product->load(['category', 'brand', 'productModel', 'creator', 'stockMovements.creator']);
        return view('products.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorizePermission('edit products');
        $categories = Category::active()->ordered()->get();
        $brands = Brand::active()->ordered()->get();
        $models = $product->brand_id ? ProductModel::active()->forBrand($product->brand_id)->get() : collect();

        return view('products.products.edit', compact('product', 'categories', 'brands', 'models'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizePermission('edit products');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku,' . $product->id],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'product_model_id' => ['nullable', 'exists:product_models,id'],
            'description' => ['nullable', 'string'],
            'specifications' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:50'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'warranty_period' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorizePermission('delete products');
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function getModelsByBrand(Request $request)
    {
        try {
            // Get brand_id from request
            $brandId = $request->input('brand_id');
            
            // Validate brand_id exists
            if (!$brandId) {
                return response()->json([
                    'error' => 'Brand ID is required'
                ], 400);
            }

            // Check if brand exists
            $brand = Brand::find($brandId);
            if (!$brand) {
                return response()->json([
                    'error' => 'Brand not found'
                ], 404);
            }

            // Get models for this brand
            $models = ProductModel::active()->forBrand($brandId)->get();
            
            return response()->json($models);
        } catch (\Exception $e) {
            \Log::error('Error loading models by brand: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
