<?php

namespace App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Inventory\Domain\Entities\StockMovement;
use App\Modules\Inventory\Domain\Repositories\StockMovementRepository;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Models\StockMovementModel;

final class EloquentStockMovementRepository implements StockMovementRepository
{
    public function save(StockMovement $movement): void
    {
        StockMovementModel::query()->create([
            'id' => $movement->id()->value,
            'tenant_id' => $movement->tenantId()->value,
            'product_id' => $movement->productId()->value,
            'user_id' => $movement->userId(),
            'direction' => 'entry',
            'type' => $movement->type()->value,
            'quantity' => $movement->quantity()->value,
            'reason' => $movement->reason()->value,
            'note' => $movement->note(),
            'unit_cost_in_cents' => $movement->unitCostInCents(),
            'occurred_at' => $movement->occurredAt()->format('Y-m-d H:i:s'),
        ]);
    }
}
