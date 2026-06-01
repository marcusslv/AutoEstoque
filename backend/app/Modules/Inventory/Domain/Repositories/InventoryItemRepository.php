<?php

namespace App\Modules\Inventory\Domain\Repositories;

use App\Modules\Inventory\Domain\Entities\InventoryItem;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

interface InventoryItemRepository
{
    public function findByProductId(TenantId $tenantId, StockProductId $productId): ?InventoryItem;

    public function save(InventoryItem $item): void;

    public function update(InventoryItem $item): void;
}
