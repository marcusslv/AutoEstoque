<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\ServiceOrder;
use App\Modules\Workshop\Domain\Factories\ServiceOrderFactory;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderRepository;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderStatus;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models\ServiceOrderModel;
use DateTimeImmutable;

final class EloquentServiceOrderRepository implements ServiceOrderRepository
{
    public function __construct(private readonly ServiceOrderFactory $serviceOrderFactory) {}

    public function findById(TenantId $tenantId, ServiceOrderId $serviceOrderId): ?ServiceOrder
    {
        $model = ServiceOrderModel::query()
            ->where('tenant_id', $tenantId->value)
            ->where('id', $serviceOrderId->value)
            ->first();

        if (! $model instanceof ServiceOrderModel) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function save(ServiceOrder $serviceOrder): void
    {
        ServiceOrderModel::query()->create([
            'id' => $serviceOrder->id()->value,
            'tenant_id' => $serviceOrder->tenantId()->value,
            'vehicle_id' => $serviceOrder->vehicleId()->value,
            'created_by_user_id' => $serviceOrder->createdByUserId(),
            'customer_name' => $serviceOrder->customerName(),
            'services_description' => $serviceOrder->servicesDescription(),
            'observations' => $serviceOrder->observations(),
            'status' => $serviceOrder->status()->value,
            'opened_at' => $serviceOrder->openedAt()->format('Y-m-d H:i:s'),
            'finished_at' => $serviceOrder->finishedAt()?->format('Y-m-d H:i:s'),
        ]);
    }

    public function update(ServiceOrder $serviceOrder): void
    {
        ServiceOrderModel::query()
            ->where('tenant_id', $serviceOrder->tenantId()->value)
            ->where('id', $serviceOrder->id()->value)
            ->update([
                'status' => $serviceOrder->status()->value,
                'finished_at' => $serviceOrder->finishedAt()?->format('Y-m-d H:i:s'),
            ]);
    }

    private function toDomain(ServiceOrderModel $model): ServiceOrder
    {
        return $this->serviceOrderFactory->create(
            id: new ServiceOrderId((string) $model->id),
            tenantId: new TenantId((string) $model->tenant_id),
            vehicleId: new VehicleId((string) $model->vehicle_id),
            createdByUserId: (string) $model->created_by_user_id,
            customerName: (string) $model->customer_name,
            servicesDescription: (string) $model->services_description,
            observations: $model->observations === null ? null : (string) $model->observations,
            status: new ServiceOrderStatus((string) $model->status),
            openedAt: new DateTimeImmutable((string) $model->opened_at),
            finishedAt: $model->finished_at === null ? null : new DateTimeImmutable((string) $model->finished_at),
        );
    }
}
