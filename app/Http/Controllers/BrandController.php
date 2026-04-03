<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('view brands');
        $query = Brand::withCount('products')->ordered();

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $brands = $query->paginate(15)->appends($request->query());
        return view('products.brands.index', compact('brands'));
    }

    public function create()
    {
        $this->authorizePermission('create brands');
        return view('products.brands.create');
    }

    public function store(Request $request)
    {
        $this->authorizePermission('create brands');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:brands,name'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        Brand::create($validated);

        return redirect()->route('brands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function show(Brand $brand)
    {
        $this->authorizePermission('view brands');
        $brand->loadCount(['products', 'models']);
        return view('products.brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        $this->authorizePermission('edit brands');
        return view('products.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $this->authorizePermission('edit brands');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:brands,name,' . $brand->id],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $brand->update($validated);

        return redirect()->route('brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        $this->authorizePermission('delete brands');
        if ($brand->products()->count() > 0) {
            return redirect()->route('brands.index')
                ->with('error', 'Cannot delete brand with existing products.');
        }

        $brand->forceDelete();

        return redirect()->route('brands.index')
            ->with('success', 'Brand deleted successfully.');
    }
}
