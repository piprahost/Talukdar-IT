<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 4 style pagination
        Paginator::defaultView('vendor.pagination.bootstrap-4');
        Paginator::defaultSimpleView('vendor.pagination.simple-bootstrap-4');

        // Share company/shop settings with sidebar and any view that includes layout
        View::composer(['partials._sidebar', 'layouts.dashboard'], function ($view) {
            try {
                $view->with('companySettings', DB::table('company_settings')->first());
            } catch (\Throwable $e) {
                $view->with('companySettings', null);
            }
        });
    }
}
