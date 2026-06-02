<?php

namespace App\Modules\Workshop\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Tenant\Application\TenantContext;
use App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos\ShowServiceOrderInput;
use App\Modules\Workshop\Application\UseCases\ShowServiceOrder\ShowServiceOrderUseCase;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotFoundException;
use App\Modules\Workshop\Interfaces\Http\Presenters\ShowServiceOrderPresenter;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ShowServiceOrderController extends Controller
{
    public function __invoke(
        string $serviceOrder,
        TenantContext $tenantContext,
        ShowServiceOrderUseCase $useCase,
        ShowServiceOrderPresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new ShowServiceOrderInput(
                tenantId: $tenantContext->id()->value,
                serviceOrderId: $serviceOrder,
            ));

            return $presenter->present($output);
        } catch (ServiceOrderNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
