<?php

namespace App\Modules\Workshop\Domain\Factories;

use App\Modules\Inventory\Domain\ValueObjects\StockMovementId;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\ServiceOrderStockMovementLink;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderItemId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderStockMovementLinkId;

final class ServiceOrderStockMovementLinkFactory
{
    public function create(
        ServiceOrderStockMovementLinkId $id,
        TenantId $tenantId,
        ServiceOrderId $serviceOrderId,
        ServiceOrderItemId $serviceOrderItemId,
        StockMovementId $stockMovementId,
    ): ServiceOrderStockMovementLink {
        return new ServiceOrderStockMovementLink(
            id: $id,
            tenantId: $tenantId,
            serviceOrderId: $serviceOrderId,
            serviceOrderItemId: $serviceOrderItemId,
            stockMovementId: $stockMovementId,
        );
    }
}
