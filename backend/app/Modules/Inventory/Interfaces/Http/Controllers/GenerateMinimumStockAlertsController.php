<?php

namespace App\Modules\Inventory\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos\GenerateMinimumStockAlertsInput;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\GenerateMinimumStockAlertsUseCase;
use App\Modules\Inventory\Interfaces\Http\Presenters\GenerateMinimumStockAlertsPresenter;
use App\Modules\Inventory\Interfaces\Http\Requests\GenerateMinimumStockAlertsRequest;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;

final class GenerateMinimumStockAlertsController extends Controller
{
    public function __invoke(
        GenerateMinimumStockAlertsRequest $request,
        TenantContext $tenantContext,
        GenerateMinimumStockAlertsUseCase $useCase,
        GenerateMinimumStockAlertsPresenter $presenter,
    ): JsonResponse {
        $output = $useCase->execute(new GenerateMinimumStockAlertsInput(
            tenantId: $tenantContext->id()->value,
            limit: $request->integer('limit', 50),
        ));

        return $presenter->present($output);
    }
}
