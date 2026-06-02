<?php

namespace App\Modules\Dashboard\Interfaces\Http\Presenters;

use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos\ListMostConsumedProductsOutput;
use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos\MostConsumedProductOutput;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;

final class ListMostConsumedProductsPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof ListMostConsumedProductsOutput);

        return response()->json([
            'data' => array_map(
                fn (MostConsumedProductOutput $item): array => [
                    'product_id' => $item->productId,
                    'product' => [
                        'name' => $item->productName,
                        'sku' => $item->productSku,
                    ],
                    'total_quantity' => $item->totalQuantity,
                    'movements_count' => $item->movementsCount,
                ],
                $output->items,
            ),
            'meta' => [
                'tenant_id' => $output->tenantId,
                'period_from' => $output->periodFrom,
                'period_to' => $output->periodTo,
                'total' => $output->total(),
            ],
        ]);
    }
}
