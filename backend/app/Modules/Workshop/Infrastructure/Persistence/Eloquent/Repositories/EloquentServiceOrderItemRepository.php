<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Workshop\Domain\Entities\ServiceOrderItem;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderItemRepository;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models\ServiceOrderItemModel;

final class EloquentServiceOrderItemRepository implements ServiceOrderItemRepository
{
    public function save(ServiceOrderItem $item): void
    {
        ServiceOrderItemModel::query()->create([
            'id' => $item->id()->value,
            'tenant_id' => $item->tenantId()->value,
            'service_order_id' => $item->serviceOrderId()->value,
            'product_id' => $item->productId()->value,
            'added_by_user_id' => $item->addedByUserId(),
            'quantity' => $item->quantity(),
        ]);
    }
}
