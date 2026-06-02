<?php

namespace App\Modules\Workshop\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Tenant\Application\TenantContext;
use App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos\ListServiceOrdersInput;
use App\Modules\Workshop\Application\UseCases\ListServiceOrders\ListServiceOrdersUseCase;
use App\Modules\Workshop\Interfaces\Http\Presenters\ListServiceOrdersPresenter;
use App\Modules\Workshop\Interfaces\Http\Requests\ListServiceOrdersRequest;
use Illuminate\Http\JsonResponse;

final class ListServiceOrdersController extends Controller
{
    public function __invoke(
        ListServiceOrdersRequest $request,
        TenantContext $tenantContext,
        ListServiceOrdersUseCase $useCase,
        ListServiceOrdersPresenter $presenter,
    ): JsonResponse {
        $output = $useCase->execute(new ListServiceOrdersInput(
            tenantId: $tenantContext->id()->value,
            status: $request->query('status'),
            term: $request->query('search'),
            openedFrom: $request->query('opened_from'),
            openedTo: $request->query('opened_to'),
            limit: $request->integer('limit', 50),
        ));

        return $presenter->present($output);
    }
}
