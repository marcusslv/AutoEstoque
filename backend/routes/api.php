<?php

use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', fn () => [
        'status' => 'ok',
        'service' => 'autoestoque-api',
    ]);

    Route::middleware('tenant')->get('/context/tenant', function (TenantContext $tenantContext) {
        return [
            'tenant_id' => $tenantContext->id()->value,
        ];
    });
});
