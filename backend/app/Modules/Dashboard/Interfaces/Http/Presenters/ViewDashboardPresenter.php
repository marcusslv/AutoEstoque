<?php

namespace App\Modules\Dashboard\Interfaces\Http\Presenters;

use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos\DashboardRecentMovementOutput;
use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos\ViewDashboardOutput;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;

final class ViewDashboardPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof ViewDashboardOutput);

        return response()->json([
            'data' => [
                'tenant_id' => $output->tenantId,
                'date' => $output->date,
                'total_products' => $output->totalProducts,
                'products_below_minimum' => $output->productsBelowMinimum,
                'products_zero_stock' => $output->productsZeroStock,
                'total_stock_value_in_cents' => $output->totalStockValueInCents,
                'today_movements' => $output->todayMovements,
                'recent_movements' => array_map(
                    fn (DashboardRecentMovementOutput $movement): array => [
                        'id' => $movement->id,
                        'product_id' => $movement->productId,
                        'product' => [
                            'name' => $movement->productName,
                            'sku' => $movement->productSku,
                        ],
                        'direction' => $movement->direction,
                        'type' => $movement->type,
                        'quantity' => $movement->quantity,
                        'reason' => $movement->reason,
                        'occurred_at' => $movement->occurredAt,
                    ],
                    $output->recentMovements,
                ),
            ],
        ]);
    }
}
