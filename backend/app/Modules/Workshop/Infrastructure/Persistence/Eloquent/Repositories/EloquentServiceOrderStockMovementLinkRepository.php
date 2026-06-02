<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Workshop\Domain\Entities\ServiceOrderStockMovementLink;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderStockMovementLinkRepository;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models\ServiceOrderStockMovementLinkModel;

final class EloquentServiceOrderStockMovementLinkRepository implements ServiceOrderStockMovementLinkRepository
{
    public function save(ServiceOrderStockMovementLink $link): void
    {
        ServiceOrderStockMovementLinkModel::query()->create([
            'id' => $link->id()->value,
            'tenant_id' => $link->tenantId()->value,
            'service_order_id' => $link->serviceOrderId()->value,
            'service_order_item_id' => $link->serviceOrderItemId()->value,
            'stock_movement_id' => $link->stockMovementId()->value,
        ]);
    }
}
