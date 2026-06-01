<?php

use App\Modules\Catalog\Interfaces\Http\Controllers\CreateProductController;
use App\Modules\Catalog\Interfaces\Http\Controllers\UpdateProductController;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', fn () => [
        'status' => 'ok',
        'service' => 'autoestoque-api',
    ]);

    Route::middleware('tenant')->group(function (): void {
        Route::get('/context/tenant', function (TenantContext $tenantContext) {
            return [
                'tenant_id' => $tenantContext->id()->value,
            ];
        });

        Route::post('/products', CreateProductController::class);
        Route::patch('/products/{product}', UpdateProductController::class);
    });
});
