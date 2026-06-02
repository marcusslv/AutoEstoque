<?php

namespace App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos;

final readonly class ServiceOrderListVehicleOutput
{
    public function __construct(
        public string $id,
        public string $plate,
        public string $brand,
        public string $model,
        public string $ownerName,
    ) {}
}
