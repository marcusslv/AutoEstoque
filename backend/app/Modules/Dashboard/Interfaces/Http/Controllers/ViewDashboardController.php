<?php

namespace App\Modules\Dashboard\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos\ViewDashboardInput;
use App\Modules\Dashboard\Application\UseCases\ViewDashboard\ViewDashboardUseCase;
use App\Modules\Dashboard\Interfaces\Http\Presenters\ViewDashboardPresenter;
use App\Modules\Dashboard\Interfaces\Http\Requests\ViewDashboardRequest;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;

final class ViewDashboardController extends Controller
{
    public function __invoke(
        ViewDashboardRequest $request,
        TenantContext $tenantContext,
        ViewDashboardUseCase $useCase,
        ViewDashboardPresenter $presenter,
    ): JsonResponse {
        $output = $useCase->execute(new ViewDashboardInput(
            tenantId: $tenantContext->id()->value,
            date: (string) ($request->query('date') ?? now()->toDateString()),
            recentMovementsLimit: $request->integer('recent_movements_limit', 5),
        ));

        return $presenter->present($output);
    }
}
