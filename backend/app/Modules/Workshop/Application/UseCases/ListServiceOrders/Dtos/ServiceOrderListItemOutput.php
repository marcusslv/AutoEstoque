<?php

namespace App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class ServiceOrderListItemOutput implements OutputDto
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $customerName,
        public string $servicesDescription,
        public ?string $observations,
        public string $status,
        public string $openedAt,
        public ?string $finishedAt,
        public ServiceOrderListVehicleOutput $vehicle,
        public int $partsTotal,
    ) {}
}
