<?php

namespace App\Modules\Workshop\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Application\Contexts\AuthenticatedUserContext;
use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use App\Modules\Tenant\Application\TenantContext;
use App\Modules\Workshop\Application\UseCases\CreateServiceOrder\CreateServiceOrderUseCase;
use App\Modules\Workshop\Application\UseCases\CreateServiceOrder\Dtos\CreateServiceOrderInput;
use App\Modules\Workshop\Domain\Exceptions\VehicleNotFoundException;
use App\Modules\Workshop\Interfaces\Http\Presenters\CreateServiceOrderPresenter;
use App\Modules\Workshop\Interfaces\Http\Requests\CreateServiceOrderRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CreateServiceOrderController extends Controller
{
    public function __invoke(
        CreateServiceOrderRequest $request,
        TenantContext $tenantContext,
        AuthenticatedUserContext $userContext,
        CreateServiceOrderUseCase $useCase,
        CreateServiceOrderPresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new CreateServiceOrderInput(
                tenantId: $tenantContext->id()->value,
                vehicleId: $request->string('vehicle_id')->toString(),
                createdByUserId: $userContext->id(),
                customerName: $request->string('customer_name')->toString(),
                servicesDescription: $request->string('services_description')->toString(),
                observations: $request->input('observations'),
            ));

            return $presenter->present($output);
        } catch (VehicleNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (DomainValidationException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
