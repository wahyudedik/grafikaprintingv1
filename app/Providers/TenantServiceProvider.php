<?php

namespace App\Providers;

use App\Services\TenantManager;
use Illuminate\Support\ServiceProvider;

class TenantServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('tenant', function ($app) {
            return new TenantManager();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->booted(function ($app) {
            if (session()->has('current_vendor_id') && !$app['tenant']->hasVendorContext()) {
                $app['tenant']->setVendorId(session('current_vendor_id'));
            }
        });
    }
}
