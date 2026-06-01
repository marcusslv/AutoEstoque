<?php

namespace App\Modules\Inventory\Interfaces\Http\Presenters;

use App\Modules\Inventory\Application\UseCases\RegisterStockOutput\Dtos\RegisterStockOutputOutput;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class RegisterStockOutputPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof RegisterStockOutputOutput);

        return response()->json([
            'data' => [
                'movement_id' => $output->movementId,
                'inventory_item_id' => $output->inventoryItemId,
                'tenant_id' => $output->tenantId,
                'product_id' => $output->productId,
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
