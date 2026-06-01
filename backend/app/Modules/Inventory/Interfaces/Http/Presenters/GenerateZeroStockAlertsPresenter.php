<?php

namespace App\Modules\Inventory\Interfaces\Http\Presenters;

use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos\GenerateZeroStockAlertsOutput;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos\ZeroStockAlertOutput;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;

final class GenerateZeroStockAlertsPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof GenerateZeroStockAlertsOutput);

        return response()->json([
            'data' => array_map(
                fn (ZeroStockAlertOutput $alert): array => [
                    'type' => 'zero_stock',
                    'product_id' => $alert->productId,
                    'product' => [
                        'name' => $alert->productName,
                        'sku' => $alert->productSku,
                    ],
                    'current_stock' => $alert->currentStock,
                    'minimum_stock' => $alert->minimumStock,
                ],
                $output->alerts,
            ),
            'meta' => [
                'total' => $output->total(),
            ],
        ]);
    }
}
