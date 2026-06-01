<?php

namespace App\Modules\Inventory\Domain\Factories;

use App\Modules\Inventory\Domain\Entities\InventoryItem;
use App\Modules\Inventory\Domain\ValueObjects\InventoryItemId;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Inventory\Domain\ValueObjects\StockQuantity;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

final class InventoryItemFactory
{
    public function create(
        InventoryItemId $id,
        TenantId $tenantId,
        StockProductId $productId,
        StockQuantity $currentStock,
    ): InventoryItem {
        return new InventoryItem(
            id: $id,
            tenantId: $tenantId,
            productId: $productId,
            currentStock: $currentStock,
        );
    }
}
