<?php

namespace App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Inventory\Domain\Entities\InventoryItem;
use App\Modules\Inventory\Domain\Factories\InventoryItemFactory;
use App\Modules\Inventory\Domain\Repositories\InventoryItemRepository;
use App\Modules\Inventory\Domain\ValueObjects\InventoryItemId;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Inventory\Domain\ValueObjects\StockQuantity;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Models\InventoryItemModel;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

final class EloquentInventoryItemRepository implements InventoryItemRepository
{
    public function __construct(private readonly InventoryItemFactory $inventoryItemFactory) {}

    public function findByProductId(TenantId $tenantId, StockProductId $productId): ?InventoryItem
    {
        $model = InventoryItemModel::query()
            ->where('tenant_id', $tenantId->value)
            ->where('product_id', $productId->value)
            ->first();

        if (! $model instanceof InventoryItemModel) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function save(InventoryItem $item): void
    {
        InventoryItemModel::query()->create([
            'id' => $item->id()->value,
            'tenant_id' => $item->tenantId()->value,
            'product_id' => $item->productId()->value,
            'current_stock' => $item->currentStock()->value,
        ]);
    }

    public function update(InventoryItem $item): void
    {
        InventoryItemModel::query()
            ->where('tenant_id', $item->tenantId()->value)
            ->where('product_id', $item->productId()->value)
            ->update([
                'current_stock' => $item->currentStock()->value,
            ]);
    }

    private function toDomain(InventoryItemModel $model): InventoryItem
    {
        return $this->inventoryItemFactory->create(
            id: new InventoryItemId((string) $model->id),
            tenantId: new TenantId((string) $model->tenant_id),
            productId: new StockProductId((string) $model->product_id),
            currentStock: new StockQuantity((int) $model->current_stock),
        );
    }
}
