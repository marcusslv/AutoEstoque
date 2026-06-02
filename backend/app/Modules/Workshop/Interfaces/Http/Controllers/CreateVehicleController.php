<?php

namespace App\Modules\Workshop\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use App\Modules\Tenant\Application\TenantContext;
use App\Modules\Workshop\Application\UseCases\CreateVehicle\CreateVehicleUseCase;
use App\Modules\Workshop\Application\UseCases\CreateVehicle\Dtos\CreateVehicleInput;
use App\Modules\Workshop\Domain\Exceptions\DuplicatedVehiclePlateException;
use App\Modules\Workshop\Domain\Exceptions\InvalidVehiclePlateException;
use App\Modules\Workshop\Interfaces\Http\Presenters\CreateVehiclePresenter;
use App\Modules\Workshop\Interfaces\Http\Requests\CreateVehicleRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CreateVehicleController extends Controller
{
    public function __invoke(
        CreateVehicleRequest $request,
        TenantContext $tenantContext,
        CreateVehicleUseCase $useCase,
        CreateVehiclePresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new CreateVehicleInput(
                tenantId: $tenantContext->id()->value,
                plate: $request->string('plate')->toString(),
                brand: $request->string('brand')->toString(),
                model: $request->string('model')->toString(),
                year: $request->integer('year'),
                ownerName: $request->string('owner_name')->toString(),
                ownerPhone: $request->string('owner_phone')->toString(),
            ));

            return $presenter->present($output);
        } catch (DuplicatedVehiclePlateException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_CONFLICT);
        } catch (InvalidVehiclePlateException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (DomainValidationException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
