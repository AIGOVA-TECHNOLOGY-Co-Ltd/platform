<?php

namespace App\Providers;

use App\Domains\Enterprise\Model\Enterprise;
use App\Domains\Enterprise\Service\EnterpriseService;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(EnterpriseService::class, function ($app) {
            return new EnterpriseService(new Enterprise());
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
