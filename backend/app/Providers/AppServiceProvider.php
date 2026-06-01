<?php

namespace App\Providers;

use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Repositories\EloquentProductRepository;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Contracts\MinimumStockAlertQuery;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Contracts\StockMovementHistoryQuery;
use App\Modules\Inventory\Domain\Repositories\InventoryItemRepository;
use App\Modules\Inventory\Domain\Repositories\StockMovementRepository;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Queries\EloquentMinimumStockAlertQuery;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Queries\EloquentStockMovementHistoryQuery;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Repositories\EloquentInventoryItemRepository;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Repositories\EloquentStockMovementRepository;
use App\Modules\Shared\Application\Contracts\TransactionManager;
use App\Modules\Shared\Infrastructure\Persistence\LaravelTransactionManager;
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
        $this->app->bind(InventoryItemRepository::class, EloquentInventoryItemRepository::class);
        $this->app->bind(StockMovementRepository::class, EloquentStockMovementRepository::class);
        $this->app->bind(MinimumStockAlertQuery::class, EloquentMinimumStockAlertQuery::class);
        $this->app->bind(StockMovementHistoryQuery::class, EloquentStockMovementHistoryQuery::class);
        $this->app->bind(TransactionManager::class, LaravelTransactionManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
