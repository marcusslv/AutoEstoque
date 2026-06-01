<?php

namespace App\Modules\Inventory\Interfaces\Http\Presenters;

use App\Modules\Inventory\Application\UseCases\RegisterStockAdjustment\Dtos\RegisterStockAdjustmentOutput;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class RegisterStockAdjustmentPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof RegisterStockAdjustmentOutput);

        return response()->json([
            'data' => [
                'movement_id' => $output->movementId,
                'inventory_item_id' => $output->inventoryItemId,
                'tenant_id' => $output->tenantId,
                'product_id' => $output->productId,
                'direction' => $output->direction,
                'type' => $output->type,
                'quantity' => $output->quantity,
                'current_stock' => $output->currentStock,
                'reason' => $output->reason,
                'note' => $output->note,
                'occurred_at' => $output->occurredAt,
            ],
        ], Response::HTTP_CREATED);
    }
}
