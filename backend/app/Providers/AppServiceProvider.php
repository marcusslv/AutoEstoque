<?php

namespace App\Providers;

use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Repositories\EloquentProductRepository;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantContext::class);
        $this->app->bind(ProductRepository::class, EloquentProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
