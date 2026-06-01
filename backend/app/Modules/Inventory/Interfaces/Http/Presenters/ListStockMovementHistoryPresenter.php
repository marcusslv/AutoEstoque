<?php

namespace App\Modules\Inventory\Interfaces\Http\Presenters;

use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos\ListStockMovementHistoryItemOutput;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos\ListStockMovementHistoryOutput;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;

final class ListStockMovementHistoryPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof ListStockMovementHistoryOutput);

        return response()->json([
            'data' => array_map(
                fn (ListStockMovementHistoryItemOutput $item): array => [
                    'id' => $item->id,
                    'tenant_id' => $item->tenantId,
                    'product_id' => $item->productId,
                    'product' => [
                        'name' => $item->productName,
                        'sku' => $item->productSku,
                    ],
                    'user_id' => $item->userId,
                    'direction' => $item->direction,
                    'type' => $item->type,
                    'quantity' => $item->quantity,
                    'reason' => $item->reason,
                    'note' => $item->note,
                    'unit_cost_in_cents' => $item->unitCostInCents,
                    'occurred_at' => $item->occurredAt,
                ],
                $output->items,
            ),
            'meta' => [
                'total' => $output->total(),
            ],
        ]);
    }
}
