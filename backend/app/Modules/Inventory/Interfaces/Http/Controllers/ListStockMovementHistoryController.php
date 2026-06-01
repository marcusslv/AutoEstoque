<?php

namespace App\Modules\Inventory\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos\ListStockMovementHistoryInput;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\ListStockMovementHistoryUseCase;
use App\Modules\Inventory\Interfaces\Http\Presenters\ListStockMovementHistoryPresenter;
use App\Modules\Inventory\Interfaces\Http\Requests\ListStockMovementHistoryRequest;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;

final class ListStockMovementHistoryController extends Controller
{
    public function __invoke(
        ListStockMovementHistoryRequest $request,
        TenantContext $tenantContext,
        ListStockMovementHistoryUseCase $useCase,
        ListStockMovementHistoryPresenter $presenter,
    ): JsonResponse {
        $output = $useCase->execute(new ListStockMovementHistoryInput(
            tenantId: $tenantContext->id()->value,
            productId: $request->query('product_id'),
            direction: $request->query('direction'),
            type: $request->query('type'),
            userId: $request->query('user_id'),
            occurredFrom: $request->query('occurred_from'),
            occurredTo: $request->query('occurred_to'),
            limit: $request->integer('limit', 50),
        ));

        return $presenter->present($output);
    }
}
