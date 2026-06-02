<?php

use App\Modules\Catalog\Interfaces\Http\Controllers\CreateProductController;
use App\Modules\Catalog\Interfaces\Http\Controllers\ListStockController;
use App\Modules\Catalog\Interfaces\Http\Controllers\UpdateProductController;
use App\Modules\Dashboard\Interfaces\Http\Controllers\ListMostConsumedProductsController;
use App\Modules\Dashboard\Interfaces\Http\Controllers\ViewDashboardController;
use App\Modules\Identity\Interfaces\Http\Controllers\AuthenticateUserController;
use App\Modules\Identity\Interfaces\Http\Controllers\CreateWorkshopUserController;
use App\Modules\Identity\Interfaces\Http\Controllers\DeactivateWorkshopUserController;
use App\Modules\Identity\Interfaces\Http\Controllers\ListWorkshopUsersController;
use App\Modules\Identity\Interfaces\Http\Controllers\RequestPasswordResetController;
use App\Modules\Identity\Interfaces\Http\Controllers\ResetPasswordController;
use App\Modules\Identity\Interfaces\Http\Controllers\UpdateWorkshopUserController;
use App\Modules\Inventory\Interfaces\Http\Controllers\GenerateMinimumStockAlertsController;
use App\Modules\Inventory\Interfaces\Http\Controllers\GenerateZeroStockAlertsController;
use App\Modules\Inventory\Interfaces\Http\Controllers\ListStockMovementHistoryController;
use App\Modules\Inventory\Interfaces\Http\Controllers\RegisterStockAdjustmentController;
use App\Modules\Inventory\Interfaces\Http\Controllers\RegisterStockEntryController;
use App\Modules\Inventory\Interfaces\Http\Controllers\RegisterStockOutputController;
use App\Modules\Tenant\Application\TenantContext;
use App\Modules\Workshop\Interfaces\Http\Controllers\AddPartToServiceOrderController;
use App\Modules\Workshop\Interfaces\Http\Controllers\CreateServiceOrderController;
use App\Modules\Workshop\Interfaces\Http\Controllers\CreateVehicleController;
use App\Modules\Workshop\Interfaces\Http\Controllers\FinishServiceOrderController;
use App\Modules\Workshop\Interfaces\Http\Controllers\ListVehiclesController;
use App\Modules\Workshop\Interfaces\Http\Controllers\ShowServiceOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', fn () => [
        'status' => 'ok',
        'service' => 'autoestoque-api',
    ]);

    Route::post('/auth/login', AuthenticateUserController::class);
    Route::post('/auth/forgot-password', RequestPasswordResetController::class);
    Route::post('/auth/reset-password', ResetPasswordController::class);

    Route::middleware('auth.api')->group(function (): void {
        Route::get('/context/tenant', function (TenantContext $tenantContext) {
            return [
                'tenant_id' => $tenantContext->id()->value,
            ];
        });

        Route::get('/users', ListWorkshopUsersController::class);
        Route::post('/users', CreateWorkshopUserController::class);
        Route::patch('/users/{user}', UpdateWorkshopUserController::class);
        Route::patch('/users/{user}/deactivate', DeactivateWorkshopUserController::class);
        Route::get('/dashboard', ViewDashboardController::class);
        Route::get('/dashboard/most-consumed-products', ListMostConsumedProductsController::class);
        Route::get('/stock', ListStockController::class);
        Route::get('/inventory/alerts/minimum-stock', GenerateMinimumStockAlertsController::class);
        Route::get('/inventory/alerts/zero-stock', GenerateZeroStockAlertsController::class);
        Route::get('/inventory/movements', ListStockMovementHistoryController::class);
        Route::post('/inventory/adjustments', RegisterStockAdjustmentController::class);
        Route::post('/inventory/entries', RegisterStockEntryController::class);
        Route::post('/inventory/outputs', RegisterStockOutputController::class);
        Route::post('/products', CreateProductController::class);
        Route::patch('/products/{product}', UpdateProductController::class);
        Route::post('/service-orders', CreateServiceOrderController::class);
        Route::get('/service-orders/{serviceOrder}', ShowServiceOrderController::class);
        Route::patch('/service-orders/{serviceOrder}/finish', FinishServiceOrderController::class);
        Route::post('/service-orders/{serviceOrder}/parts', AddPartToServiceOrderController::class);
        Route::get('/vehicles', ListVehiclesController::class);
        Route::post('/vehicles', CreateVehicleController::class);
    });
});
