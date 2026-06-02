<?php

namespace App\Modules\Workshop\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Identity\Application\Contexts\AuthenticatedUserContext;
use App\Modules\Inventory\Domain\Exceptions\InsufficientStockException;
use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use App\Modules\Tenant\Application\TenantContext;
use App\Modules\Workshop\Application\UseCases\AddPartToServiceOrder\AddPartToServiceOrderUseCase;
use App\Modules\Workshop\Application\UseCases\AddPartToServiceOrder\Dtos\AddPartToServiceOrderInput;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotFoundException;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotOpenException;
use App\Modules\Workshop\Interfaces\Http\Presenters\AddPartToServiceOrderPresenter;
use App\Modules\Workshop\Interfaces\Http\Requests\AddPartToServiceOrderRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class AddPartToServiceOrderController extends Controller
{
    public function __invoke(
        string $serviceOrder,
        AddPartToServiceOrderRequest $request,
        TenantContext $tenantContext,
        AuthenticatedUserContext $userContext,
        AddPartToServiceOrderUseCase $useCase,
        AddPartToServiceOrderPresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new AddPartToServiceOrderInput(
                tenantId: $tenantContext->id()->value,
                serviceOrderId: $serviceOrder,
                productId: $request->string('product_id')->toString(),
                addedByUserId: $userContext->id(),
                quantity: $request->integer('quantity'),
            ));

            return $presenter->present($output);
        } catch (ServiceOrderNotFoundException|ProductNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (ServiceOrderNotOpenException|InsufficientStockException $exception) {
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
