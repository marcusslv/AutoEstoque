<?php

namespace App\Modules\Dashboard\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos\ListMostConsumedProductsInput;
use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\ListMostConsumedProductsUseCase;
use App\Modules\Dashboard\Interfaces\Http\Presenters\ListMostConsumedProductsPresenter;
use App\Modules\Dashboard\Interfaces\Http\Requests\ListMostConsumedProductsRequest;
use App\Modules\Tenant\Application\TenantContext;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

final class ListMostConsumedProductsController extends Controller
{
    public function __invoke(
        ListMostConsumedProductsRequest $request,
        TenantContext $tenantContext,
        ListMostConsumedProductsUseCase $useCase,
        ListMostConsumedProductsPresenter $presenter,
    ): JsonResponse {
        $now = CarbonImmutable::now();

        $output = $useCase->execute(new ListMostConsumedProductsInput(
            tenantId: $tenantContext->id()->value,
            periodFrom: (string) ($request->query('period_from') ?? $now->startOfMonth()->toDateString()),
            periodTo: (string) ($request->query('period_to') ?? $now->toDateString()),
            limit: $request->integer('limit', 10),
        ));

        return $presenter->present($output);
    }
}
