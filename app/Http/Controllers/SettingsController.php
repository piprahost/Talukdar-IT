<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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
        $viewData = [
            'categories' => $categories,
            'currentCategory' => $category,
            'defs' => $defs,
            'values' => $values,
        ];
        if ($category === 'pdf_design') {
            $viewData['company'] = \App\Http\Controllers\CompanyInfoController::getCompanySettings();
        }
        return view('settings.app.edit', $viewData);
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
            } elseif ($type === 'textarea') {
                $rules[$key] = 'nullable|string|max:10000';
            } else {
                $rules[$key] = 'nullable|string|max:1000';
            }
        }
        if ($category === 'pdf_design' && $request->hasFile('logo_upload')) {
            $rules['logo_upload'] = 'nullable|image|mimes:jpeg,png,gif,webp,svg|max:2048';
        }
        $request->validate($rules);

        if ($category === 'pdf_design' && $request->hasFile('logo_upload')) {
            $file = $request->file('logo_upload');
            $ext = $file->getClientOriginalExtension() ?: $file->guessExtension();
            $filename = 'logo.' . ($ext === 'jpeg' ? 'jpg' : $ext);
            $dir = 'pdf-design';
            $oldUrl = Setting::get($category . '.logo_url', null);
            if ($oldUrl && str_starts_with($oldUrl, $dir . '/')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldUrl);
            }
            $path = $file->storeAs($dir, $filename, 'public');
            Setting::set($category, 'logo_url', $path);
        }

        $skipLogoUrl = $category === 'pdf_design' && $request->hasFile('logo_upload');
        foreach ($defs as $key => $def) {
            if ($skipLogoUrl && $key === 'logo_url') {
                continue;
            }
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
     * Recalculate return totals from line items (lightweight, no Artisan). Sales/purchase recalc via terminal.
     */
    public function recalculateTotals()
    {
        $this->authorizePermission('edit settings');

        // Fix sale return totals with raw SQL (no model loading = low memory)
        $saleReturnsUpdated = DB::update('
            UPDATE sales_returns sr
            INNER JOIN (
                SELECT sale_return_id, COALESCE(SUM(subtotal), 0) AS item_subtotal
                FROM sale_return_items
                GROUP BY sale_return_id
            ) t ON t.sale_return_id = sr.id
            SET sr.subtotal = t.item_subtotal,
                sr.total_amount = t.item_subtotal + COALESCE(sr.tax_amount, 0) - COALESCE(sr.discount_amount, 0)
            WHERE sr.total_amount <= 0
        ');

        // Fix purchase return totals with raw SQL
        $purchaseReturnsUpdated = DB::update('
            UPDATE purchase_returns pr
            INNER JOIN (
                SELECT purchase_return_id, COALESCE(SUM(subtotal), 0) AS item_subtotal
                FROM purchase_return_items
                GROUP BY purchase_return_id
            ) t ON t.purchase_return_id = pr.id
            SET pr.subtotal = t.item_subtotal,
                pr.total_amount = t.item_subtotal + COALESCE(pr.tax_amount, 0) - COALESCE(pr.discount_amount, 0)
            WHERE pr.total_amount <= 0
        ');

        $message = 'Return totals recalculated.';
        if ($saleReturnsUpdated > 0 || $purchaseReturnsUpdated > 0) {
            $message .= " Updated {$saleReturnsUpdated} sale return(s), {$purchaseReturnsUpdated} purchase return(s).";
        }
        $message .= ' For sales/purchases recalc run in terminal: php artisan sales:recalculate-totals && php artisan purchases:recalculate-totals';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Recalculate sale and purchase return totals from line items (fixes Return Amount when 0).
     */
    public function recalculateReturns()
    {
        $this->authorizePermission('edit settings');

        $saleReturnsUpdated = DB::update('
            UPDATE sales_returns sr
            INNER JOIN (
                SELECT sale_return_id, COALESCE(SUM(subtotal), 0) AS item_subtotal
                FROM sale_return_items
                GROUP BY sale_return_id
            ) t ON t.sale_return_id = sr.id
            SET sr.subtotal = t.item_subtotal,
                sr.total_amount = t.item_subtotal + COALESCE(sr.tax_amount, 0) - COALESCE(sr.discount_amount, 0)
            WHERE sr.total_amount <= 0
        ');

        $purchaseReturnsUpdated = DB::update('
            UPDATE purchase_returns pr
            INNER JOIN (
                SELECT purchase_return_id, COALESCE(SUM(subtotal), 0) AS item_subtotal
                FROM purchase_return_items
                GROUP BY purchase_return_id
            ) t ON t.purchase_return_id = pr.id
            SET pr.subtotal = t.item_subtotal,
                pr.total_amount = t.item_subtotal + COALESCE(pr.tax_amount, 0) - COALESCE(pr.discount_amount, 0)
            WHERE pr.total_amount <= 0
        ');

        $message = 'Return totals recalculated.';
        if ($saleReturnsUpdated > 0 || $purchaseReturnsUpdated > 0) {
            $message .= " Updated {$saleReturnsUpdated} sale return(s), {$purchaseReturnsUpdated} purchase return(s).";
        } else {
            $message .= ' No returns needed updating.';
        }

        return redirect()->back()->with('success', $message);
    }
}
