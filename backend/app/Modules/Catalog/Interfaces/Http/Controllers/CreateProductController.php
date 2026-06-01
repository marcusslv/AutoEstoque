<?php

namespace App\Modules\Catalog\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\UseCases\CreateProduct\CreateProductInput;
use App\Modules\Catalog\Application\UseCases\CreateProduct\CreateProductUseCase;
use App\Modules\Catalog\Domain\Exceptions\DuplicatedBarcodeException;
use App\Modules\Catalog\Domain\Exceptions\DuplicatedSkuException;
use App\Modules\Catalog\Interfaces\Http\Presenters\CreateProductPresenter;
use App\Modules\Catalog\Interfaces\Http\Requests\CreateProductRequest;
use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CreateProductController extends Controller
{
    public function __invoke(
        CreateProductRequest $request,
        TenantContext $tenantContext,
        CreateProductUseCase $useCase,
        CreateProductPresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new CreateProductInput(
                tenantId: $tenantContext->id()->value,
                name: $request->string('name')->toString(),
                sku: $request->string('sku')->toString(),
                barcode: $request->input('barcode'),
                category: $request->input('category'),
                brand: $request->input('brand'),
                supplier: $request->input('supplier'),
                minimumStock: $request->integer('minimum_stock'),
                costInCents: $request->integer('cost_in_cents'),
                currency: $request->string('currency', 'BRL')->toString(),
            ));

            return $presenter->present($output);
        } catch (DuplicatedSkuException|DuplicatedBarcodeException $exception) {
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
