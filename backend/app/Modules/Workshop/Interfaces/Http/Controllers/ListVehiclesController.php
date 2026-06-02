<?php

namespace App\Modules\Workshop\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Tenant\Application\TenantContext;
use App\Modules\Workshop\Application\UseCases\ListVehicles\Dtos\ListVehiclesInput;
use App\Modules\Workshop\Application\UseCases\ListVehicles\ListVehiclesUseCase;
use App\Modules\Workshop\Interfaces\Http\Presenters\ListVehiclesPresenter;
use App\Modules\Workshop\Interfaces\Http\Requests\ListVehiclesRequest;
use Illuminate\Http\JsonResponse;

final class ListVehiclesController extends Controller
{
    public function __invoke(
        ListVehiclesRequest $request,
        TenantContext $tenantContext,
        ListVehiclesUseCase $useCase,
        ListVehiclesPresenter $presenter,
    ): JsonResponse {
        $output = $useCase->execute(new ListVehiclesInput(
            tenantId: $tenantContext->id()->value,
            term: $request->query('search'),
            limit: $request->integer('limit', 50),
        ));

        return $presenter->present($output);
    }
}
