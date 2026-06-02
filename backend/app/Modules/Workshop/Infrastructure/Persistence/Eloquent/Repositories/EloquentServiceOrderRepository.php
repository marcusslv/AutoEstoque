<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Workshop\Domain\Entities\ServiceOrder;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderRepository;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models\ServiceOrderModel;

final class EloquentServiceOrderRepository implements ServiceOrderRepository
{
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
        ]);
    }
}
