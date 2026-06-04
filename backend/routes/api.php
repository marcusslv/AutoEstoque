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
use App\Modules\Identity\Interfaces\Http\Controllers\LogoutUserController;
use App\Modules\Identity\Interfaces\Http\Controllers\RequestPasswordResetController;
use App\Modules\Identity\Interfaces\Http\Controllers\ResetPasswordController;
use App\Modules\Identity\Interfaces\Http\Controllers\UpdateWorkshopUserController;
use App\Modules\Inventory\Interfaces\Http\Controllers\GenerateMinimumStockAlertsController;
use App\Modules\Inventory\Interfaces\Http\Controllers\GenerateZeroStockAlertsController;
use App\Modules\Inventory\Interfaces\Http\Controllers\ListStockMovementHistoryController;
use App\Modules\Inventory\Interfaces\Http\Controllers\RegisterStockAdjustmentController;
use App\Modules\Inventory\Interfaces\Http\Controllers\RegisterStockEntryController;
use App\Modules\Inventory\Interfaces\Http\Controllers\RegisterStockOutputController;
use App\Modules\Settings\Interfaces\Http\Controllers\GetWorkshopSettingsController;
use App\Modules\Settings\Interfaces\Http\Controllers\UpdateWorkshopSettingsController;
use App\Modules\Tenant\Application\TenantContext;
use App\Modules\Workshop\Interfaces\Http\Controllers\AddPartToServiceOrderController;
use App\Modules\Workshop\Interfaces\Http\Controllers\CreateServiceOrderController;
use App\Modules\Workshop\Interfaces\Http\Controllers\CreateVehicleController;
use App\Modules\Workshop\Interfaces\Http\Controllers\FinishServiceOrderController;
use App\Modules\Workshop\Interfaces\Http\Controllers\ListServiceOrdersController;
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
        $backOfficeRoles = 'role:owner,manager,admin';
        $workshopRoles = 'role:owner,manager,admin,mechanic';

        Route::post('/auth/logout', LogoutUserController::class);

        Route::get('/context/tenant', function (TenantContext $tenantContext) {
            return [
                'tenant_id' => $tenantContext->id()->value,
            ];
        });

        Route::get('/users', ListWorkshopUsersController::class)->middleware($backOfficeRoles);
        Route::post('/users', CreateWorkshopUserController::class)->middleware($backOfficeRoles);
        Route::patch('/users/{user}', UpdateWorkshopUserController::class)->middleware($backOfficeRoles);
        Route::patch('/users/{user}/deactivate', DeactivateWorkshopUserController::class)->middleware($backOfficeRoles);
        Route::get('/settings/workshop', GetWorkshopSettingsController::class)->middleware($backOfficeRoles);
        Route::patch('/settings/workshop', UpdateWorkshopSettingsController::class)->middleware($backOfficeRoles);
        Route::get('/dashboard', ViewDashboardController::class)->middleware($backOfficeRoles);
        Route::get('/dashboard/most-consumed-products', ListMostConsumedProductsController::class)->middleware($backOfficeRoles);
        Route::get('/stock', ListStockController::class)->middleware($workshopRoles);
        Route::get('/inventory/alerts/minimum-stock', GenerateMinimumStockAlertsController::class)->middleware($backOfficeRoles);
        Route::get('/inventory/alerts/zero-stock', GenerateZeroStockAlertsController::class)->middleware($backOfficeRoles);
        Route::get('/inventory/movements', ListStockMovementHistoryController::class)->middleware($backOfficeRoles);
        Route::post('/inventory/adjustments', RegisterStockAdjustmentController::class)->middleware($backOfficeRoles);
        Route::post('/inventory/entries', RegisterStockEntryController::class)->middleware($backOfficeRoles);
        Route::post('/inventory/outputs', RegisterStockOutputController::class)->middleware($backOfficeRoles);
        Route::post('/products', CreateProductController::class)->middleware($backOfficeRoles);
        Route::patch('/products/{product}', UpdateProductController::class)->middleware($backOfficeRoles);
        Route::get('/service-orders', ListServiceOrdersController::class)->middleware($workshopRoles);
        Route::post('/service-orders', CreateServiceOrderController::class)->middleware($workshopRoles);
        Route::get('/service-orders/{serviceOrder}', ShowServiceOrderController::class)->middleware($workshopRoles);
        Route::patch('/service-orders/{serviceOrder}/finish', FinishServiceOrderController::class)->middleware($workshopRoles);
        Route::post('/service-orders/{serviceOrder}/parts', AddPartToServiceOrderController::class)->middleware($workshopRoles);
        Route::get('/vehicles', ListVehiclesController::class)->middleware($workshopRoles);
        Route::post('/vehicles', CreateVehicleController::class)->middleware($workshopRoles);
    });
});
