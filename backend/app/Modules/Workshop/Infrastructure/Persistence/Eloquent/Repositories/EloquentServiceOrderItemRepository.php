<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\ServiceOrderItem;
use App\Modules\Workshop\Domain\Factories\ServiceOrderItemFactory;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderItemRepository;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderItemId;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models\ServiceOrderItemModel;

final class EloquentServiceOrderItemRepository implements ServiceOrderItemRepository
{
    public function __construct(private readonly ServiceOrderItemFactory $serviceOrderItemFactory) {}

    public function listByServiceOrder(TenantId $tenantId, ServiceOrderId $serviceOrderId): array
    {
        return ServiceOrderItemModel::query()
            ->where('tenant_id', $tenantId->value)
            ->where('service_order_id', $serviceOrderId->value)
            ->orderBy('created_at')
            ->get()
            ->map(fn (ServiceOrderItemModel $model): ServiceOrderItem => $this->toDomain($model))
            ->all();
    }

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

    private function toDomain(ServiceOrderItemModel $model): ServiceOrderItem
    {
        return $this->serviceOrderItemFactory->create(
            id: new ServiceOrderItemId((string) $model->id),
            tenantId: new TenantId((string) $model->tenant_id),
            serviceOrderId: new ServiceOrderId((string) $model->service_order_id),
            productId: new ProductId((string) $model->product_id),
            addedByUserId: (string) $model->added_by_user_id,
            quantity: (int) $model->quantity,
        );
    }
}
