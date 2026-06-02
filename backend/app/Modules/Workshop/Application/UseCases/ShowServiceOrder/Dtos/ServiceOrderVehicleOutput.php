<?php

namespace App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos;

final readonly class ServiceOrderVehicleOutput
{
    public function __construct(
        public string $id,
        public string $plate,
        public string $brand,
        public string $model,
        public int $year,
        public string $ownerName,
        public string $ownerPhone,
    ) {}
}
