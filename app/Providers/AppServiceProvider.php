<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/settings_helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 4 style pagination
        Paginator::defaultView('vendor.pagination.bootstrap-4');
        Paginator::defaultSimpleView('vendor.pagination.simple-bootstrap-4');

        // Apply timezone from settings (early in request)
        if (function_exists('settings')) {
            $tz = settings('general.timezone');
            if ($tz && is_string($tz)) {
                try {
                    date_default_timezone_set($tz);
                    config(['app.timezone' => $tz]);
                } catch (\Throwable $e) {
                    // ignore invalid timezone
                }
            }
        }

        // Share company/shop settings with sidebar and any view that includes layout
        View::composer(['partials._sidebar', 'layouts.dashboard'], function ($view) {
            try {
                $view->with('companySettings', DB::table('company_settings')->first());
            } catch (\Throwable $e) {
                $view->with('companySettings', null);
            }
        });

        // Share app settings (currency, date format) with all dashboard views for dynamic display
        View::composer('layouts.dashboard', function ($view) {
            try {
                $view->with('currencySymbol', function_exists('settings') ? (settings('general.currency_symbol') ?: '৳') : '৳');
                $view->with('currencyCode', function_exists('settings') ? (settings('general.currency_code') ?: 'BDT') : 'BDT');
                $view->with('dateFormat', function_exists('settings') ? (settings('general.date_format') ?: 'd/m/Y') : 'd/m/Y');
            } catch (\Throwable $e) {
                $view->with('currencySymbol', '৳')->with('currencyCode', 'BDT')->with('dateFormat', 'd/m/Y');
            }
        });
    }
}

/**
 * Get an app setting value by key (e.g. 'sales.invoice_prefix' or 'general.date_format').
 * Uses config/settings.php definitions and database; cached per key.
 */
if (!function_exists('settings')) {
    function settings(string $key, $default = null): mixed
    {
        return \App\Models\Setting::get($key, $default);
    }
}
