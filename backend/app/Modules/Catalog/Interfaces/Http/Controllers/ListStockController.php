<?php

namespace App\Modules\Catalog\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\UseCases\ListStock\Dtos\ListStockInput;
use App\Modules\Catalog\Application\UseCases\ListStock\ListStockUseCase;
use App\Modules\Catalog\Interfaces\Http\Presenters\ListStockPresenter;
use App\Modules\Catalog\Interfaces\Http\Requests\ListStockRequest;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;

final class ListStockController extends Controller
{
    public function __invoke(
        ListStockRequest $request,
        TenantContext $tenantContext,
        ListStockUseCase $useCase,
        ListStockPresenter $presenter,
    ): JsonResponse {
        $output = $useCase->execute(new ListStockInput(
            tenantId: $tenantContext->id()->value,
            term: $request->query('search'),
        ));

        return $presenter->present($output);
    }
}
