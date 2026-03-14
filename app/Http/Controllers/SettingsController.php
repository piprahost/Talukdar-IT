<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * Show app settings (redirect to first category).
     */
    public function index()
    {
        $this->authorizePermission('view settings');
        $categories = Setting::getCategories();
        $first = array_key_first($categories);
        if ($first) {
            return redirect()->route('settings.app.edit', ['category' => $first]);
        }
        return view('settings.app.index', ['categories' => $categories]);
    }

    /**
     * Show form for one category.
     */
    public function edit(string $category)
    {
        $this->authorizePermission('view settings');
        $categories = Setting::getCategories();
        if (!isset($categories[$category])) {
            return redirect()->route('settings.app.index')->with('error', 'Invalid settings category.');
        }
        $defs = Setting::getDefinitionsForCategory($category);
        $values = Setting::getByCategory($category);
        return view('settings.app.edit', [
            'categories' => $categories,
            'currentCategory' => $category,
            'defs' => $defs,
            'values' => $values,
        ]);
    }

    /**
     * Update settings for one category.
     */
    public function update(Request $request, string $category)
    {
        $this->authorizePermission('edit settings');
        $categories = Setting::getCategories();
        if (!isset($categories[$category])) {
            return redirect()->route('settings.app.index')->with('error', 'Invalid settings category.');
        }
        $defs = Setting::getDefinitionsForCategory($category);
        $rules = [];
        foreach ($defs as $key => $def) {
            $type = $def['type'] ?? 'text';
            if ($type === 'boolean') {
                $rules[$key] = 'nullable';
            } elseif ($type === 'integer') {
                $rules[$key] = 'nullable|integer';
            } else {
                $rules[$key] = 'nullable|string|max:1000';
            }
        }
        $request->validate($rules);
        foreach ($defs as $key => $def) {
            $type = $def['type'] ?? 'text';
            $value = $type === 'boolean' ? $request->boolean($key) : $request->input($key, $def['default'] ?? null);
            Setting::set($category, $key, $value);
        }
        $label = $categories[$category]['label'] ?? $category;
        return redirect()->route('settings.app.edit', ['category' => $category])
            ->with('success', "{$label} settings saved successfully.");
    }

    /**
     * Clear application cache (visible from settings UI).
     */
    public function clearCache()
    {
        $this->authorizePermission('edit settings');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        return redirect()->back()->with('success', 'Cache cleared successfully.');
    }

    /**
     * Recalculate sales and purchase totals from line items (visible from settings UI).
     */
    public function recalculateTotals()
    {
        $this->authorizePermission('edit settings');
        Artisan::call('sales:recalculate-totals');
        Artisan::call('purchases:recalculate-totals');
        return redirect()->back()->with('success', 'Sales and purchase totals recalculated successfully.');
    }
}
