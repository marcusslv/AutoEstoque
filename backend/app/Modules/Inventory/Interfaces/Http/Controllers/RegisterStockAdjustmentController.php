<?php

namespace App\Modules\Inventory\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Inventory\Application\UseCases\RegisterStockAdjustment\Dtos\RegisterStockAdjustmentInput;
use App\Modules\Inventory\Application\UseCases\RegisterStockAdjustment\RegisterStockAdjustmentUseCase;
use App\Modules\Inventory\Domain\Exceptions\InsufficientStockException;
use App\Modules\Inventory\Interfaces\Http\Presenters\RegisterStockAdjustmentPresenter;
use App\Modules\Inventory\Interfaces\Http\Requests\RegisterStockAdjustmentRequest;
use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class RegisterStockAdjustmentController extends Controller
{
    public function __invoke(
        RegisterStockAdjustmentRequest $request,
        TenantContext $tenantContext,
        RegisterStockAdjustmentUseCase $useCase,
        RegisterStockAdjustmentPresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new RegisterStockAdjustmentInput(
                tenantId: $tenantContext->id()->value,
                userId: (string) $request->header('X-User-Id'),
                productId: $request->string('product_id')->toString(),
                direction: $request->string('direction')->toString(),
                quantity: $request->integer('quantity'),
                reason: $request->string('reason')->toString(),
                note: $request->input('note'),
            ));

            return $presenter->present($output);
        } catch (ProductNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (InsufficientStockException $exception) {
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
