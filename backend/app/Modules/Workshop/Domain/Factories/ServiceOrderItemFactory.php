<?php

namespace App\Modules\Workshop\Domain\Factories;

use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\ServiceOrderItem;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderItemId;

final class ServiceOrderItemFactory
{
    public function create(
        ServiceOrderItemId $id,
        TenantId $tenantId,
        ServiceOrderId $serviceOrderId,
        ProductId $productId,
        string $addedByUserId,
        int $quantity,
    ): ServiceOrderItem {
        return new ServiceOrderItem(
            id: $id,
            tenantId: $tenantId,
            serviceOrderId: $serviceOrderId,
            productId: $productId,
            addedByUserId: trim($addedByUserId),
            quantity: $quantity,
        );
    }
}
