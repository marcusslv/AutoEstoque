<?php

namespace App\Modules\Inventory\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Inventory\Application\UseCases\RegisterStockOutput\Dtos\RegisterStockOutputInput;
use App\Modules\Inventory\Application\UseCases\RegisterStockOutput\RegisterStockOutputUseCase;
use App\Modules\Inventory\Domain\Exceptions\InsufficientStockException;
use App\Modules\Inventory\Interfaces\Http\Presenters\RegisterStockOutputPresenter;
use App\Modules\Inventory\Interfaces\Http\Requests\RegisterStockOutputRequest;
use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class RegisterStockOutputController extends Controller
{
    public function __invoke(
        RegisterStockOutputRequest $request,
        TenantContext $tenantContext,
        RegisterStockOutputUseCase $useCase,
        RegisterStockOutputPresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new RegisterStockOutputInput(
                tenantId: $tenantContext->id()->value,
                userId: (string) $request->header('X-User-Id'),
                productId: $request->string('product_id')->toString(),
                type: $request->string('type')->toString(),
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
