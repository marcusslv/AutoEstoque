<?php

namespace App\Modules\Inventory\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Identity\Application\Contexts\AuthenticatedUserContext;
use App\Modules\Inventory\Application\UseCases\RegisterStockEntry\Dtos\RegisterStockEntryInput;
use App\Modules\Inventory\Application\UseCases\RegisterStockEntry\RegisterStockEntryUseCase;
use App\Modules\Inventory\Interfaces\Http\Presenters\RegisterStockEntryPresenter;
use App\Modules\Inventory\Interfaces\Http\Requests\RegisterStockEntryRequest;
use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class RegisterStockEntryController extends Controller
{
    public function __invoke(
        RegisterStockEntryRequest $request,
        TenantContext $tenantContext,
        AuthenticatedUserContext $userContext,
        RegisterStockEntryUseCase $useCase,
        RegisterStockEntryPresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new RegisterStockEntryInput(
                tenantId: $tenantContext->id()->value,
                userId: $userContext->id(),
                productId: $request->string('product_id')->toString(),
                type: $request->string('type')->toString(),
                quantity: $request->integer('quantity'),
                reason: $request->string('reason')->toString(),
                note: $request->input('note'),
                unitCostInCents: $request->has('unit_cost_in_cents') ? $request->integer('unit_cost_in_cents') : null,
            ));

            return $presenter->present($output);
        } catch (ProductNotFoundException $exception) {
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
