<?php

namespace App\Modules\Workshop\Domain\Factories;

use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\ServiceOrder;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderStatus;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use DateTimeImmutable;

final class ServiceOrderFactory
{
    public function create(
        ServiceOrderId $id,
        TenantId $tenantId,
        VehicleId $vehicleId,
        string $createdByUserId,
        string $customerName,
        string $servicesDescription,
        ?string $observations,
        ServiceOrderStatus $status,
        DateTimeImmutable $openedAt,
    ): ServiceOrder {
        return new ServiceOrder(
            id: $id,
            tenantId: $tenantId,
            vehicleId: $vehicleId,
            createdByUserId: trim($createdByUserId),
            customerName: trim($customerName),
            servicesDescription: trim($servicesDescription),
            observations: $this->nullableTrim($observations),
            status: $status,
            openedAt: $openedAt,
        );
    }

    private function nullableTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
