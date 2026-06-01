<?php

namespace App\Modules\Inventory\Interfaces\Http\Presenters;

use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos\GenerateMinimumStockAlertsOutput;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos\MinimumStockAlertOutput;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;

final class GenerateMinimumStockAlertsPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof GenerateMinimumStockAlertsOutput);

        return response()->json([
            'data' => array_map(
                fn (MinimumStockAlertOutput $alert): array => [
                    'type' => 'minimum_stock',
                    'product_id' => $alert->productId,
                    'product' => [
                        'name' => $alert->productName,
                        'sku' => $alert->productSku,
                    ],
                    'current_stock' => $alert->currentStock,
                    'minimum_stock' => $alert->minimumStock,
                    'shortage_quantity' => $alert->shortageQuantity,
                ],
                $output->alerts,
            ),
            'meta' => [
                'total' => $output->total(),
            ],
        ]);
    }
}
