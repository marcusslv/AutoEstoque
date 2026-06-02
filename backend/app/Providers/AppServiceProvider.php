<?php

namespace App\Providers;

use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Repositories\EloquentProductRepository;
use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Contracts\MostConsumedProductsQuery;
use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Contracts\DashboardQuery;
use App\Modules\Dashboard\Infrastructure\Persistence\Eloquent\Queries\EloquentDashboardQuery;
use App\Modules\Dashboard\Infrastructure\Persistence\Eloquent\Queries\EloquentMostConsumedProductsQuery;
use App\Modules\Identity\Application\Contexts\AuthenticatedUserContext;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Contracts\AccessTokenIssuer;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Contracts\CredentialsVerifier;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Contracts\WorkshopUserRepository;
use App\Modules\Identity\Application\UseCases\RequestPasswordReset\Contracts\PasswordResetLinkSender;
use App\Modules\Identity\Application\UseCases\ResetPassword\Contracts\PasswordResetter;
use App\Modules\Identity\Infrastructure\Persistence\Eloquent\EloquentAccessTokenIssuer;
use App\Modules\Identity\Infrastructure\Persistence\Eloquent\EloquentCredentialsVerifier;
use App\Modules\Identity\Infrastructure\Persistence\Eloquent\EloquentWorkshopUserRepository;
use App\Modules\Identity\Infrastructure\Persistence\Eloquent\LaravelPasswordResetLinkSender;
use App\Modules\Identity\Infrastructure\Persistence\Eloquent\LaravelPasswordResetter;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Contracts\MinimumStockAlertQuery;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Contracts\ZeroStockAlertQuery;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Contracts\StockMovementHistoryQuery;
use App\Modules\Inventory\Domain\Repositories\InventoryItemRepository;
use App\Modules\Inventory\Domain\Repositories\StockMovementRepository;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Queries\EloquentMinimumStockAlertQuery;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Queries\EloquentStockMovementHistoryQuery;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Queries\EloquentZeroStockAlertQuery;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Repositories\EloquentInventoryItemRepository;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Repositories\EloquentStockMovementRepository;
use App\Modules\Shared\Application\Contracts\TransactionManager;
use App\Modules\Shared\Infrastructure\Persistence\LaravelTransactionManager;
use App\Modules\Tenant\Application\TenantContext;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderItemRepository;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderRepository;
use App\Modules\Workshop\Domain\Repositories\VehicleRepository;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Repositories\EloquentServiceOrderItemRepository;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Repositories\EloquentServiceOrderRepository;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Repositories\EloquentVehicleRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantContext::class);
        $this->app->singleton(AuthenticatedUserContext::class);
        $this->app->bind(DashboardQuery::class, EloquentDashboardQuery::class);
        $this->app->bind(MostConsumedProductsQuery::class, EloquentMostConsumedProductsQuery::class);
        $this->app->bind(CredentialsVerifier::class, EloquentCredentialsVerifier::class);
        $this->app->bind(AccessTokenIssuer::class, EloquentAccessTokenIssuer::class);
        $this->app->bind(WorkshopUserRepository::class, EloquentWorkshopUserRepository::class);
        $this->app->bind(PasswordResetLinkSender::class, LaravelPasswordResetLinkSender::class);
        $this->app->bind(PasswordResetter::class, LaravelPasswordResetter::class);
        $this->app->bind(ProductRepository::class, EloquentProductRepository::class);
        $this->app->bind(VehicleRepository::class, EloquentVehicleRepository::class);
        $this->app->bind(ServiceOrderRepository::class, EloquentServiceOrderRepository::class);
        $this->app->bind(ServiceOrderItemRepository::class, EloquentServiceOrderItemRepository::class);
        $this->app->bind(InventoryItemRepository::class, EloquentInventoryItemRepository::class);
        $this->app->bind(StockMovementRepository::class, EloquentStockMovementRepository::class);
        $this->app->bind(MinimumStockAlertQuery::class, EloquentMinimumStockAlertQuery::class);
        $this->app->bind(ZeroStockAlertQuery::class, EloquentZeroStockAlertQuery::class);
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
