<?php

namespace App\Modules\Workshop\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Identity\Application\Contexts\AuthenticatedUserContext;
use App\Modules\Inventory\Domain\Exceptions\InsufficientStockException;
use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use App\Modules\Tenant\Application\TenantContext;
use App\Modules\Workshop\Application\UseCases\FinishServiceOrder\Dtos\FinishServiceOrderInput;
use App\Modules\Workshop\Application\UseCases\FinishServiceOrder\FinishServiceOrderUseCase;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderHasNoItemsException;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotFoundException;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotOpenException;
use App\Modules\Workshop\Interfaces\Http\Presenters\FinishServiceOrderPresenter;
use App\Modules\Workshop\Interfaces\Http\Requests\FinishServiceOrderRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class FinishServiceOrderController extends Controller
{
    public function __invoke(
        string $serviceOrder,
        FinishServiceOrderRequest $request,
        TenantContext $tenantContext,
        AuthenticatedUserContext $userContext,
        FinishServiceOrderUseCase $useCase,
        FinishServiceOrderPresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new FinishServiceOrderInput(
                tenantId: $tenantContext->id()->value,
                serviceOrderId: $serviceOrder,
                finishedByUserId: $userContext->id(),
            ));

            return $presenter->present($output);
        } catch (ServiceOrderNotFoundException|ProductNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (ServiceOrderNotOpenException|ServiceOrderHasNoItemsException|InsufficientStockException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_CONFLICT);
        } catch (DomainValidationException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
