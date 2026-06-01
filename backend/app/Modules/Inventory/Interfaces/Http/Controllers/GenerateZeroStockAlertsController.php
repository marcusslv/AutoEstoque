<?php

namespace App\Modules\Inventory\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos\GenerateZeroStockAlertsInput;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\GenerateZeroStockAlertsUseCase;
use App\Modules\Inventory\Interfaces\Http\Presenters\GenerateZeroStockAlertsPresenter;
use App\Modules\Inventory\Interfaces\Http\Requests\GenerateZeroStockAlertsRequest;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;

final class GenerateZeroStockAlertsController extends Controller
{
    public function __invoke(
        GenerateZeroStockAlertsRequest $request,
        TenantContext $tenantContext,
        GenerateZeroStockAlertsUseCase $useCase,
        GenerateZeroStockAlertsPresenter $presenter,
    ): JsonResponse {
        $output = $useCase->execute(new GenerateZeroStockAlertsInput(
            tenantId: $tenantContext->id()->value,
            limit: $request->integer('limit', 50),
        ));

        return $presenter->present($output);
    }
}
