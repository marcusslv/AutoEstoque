<?php

namespace App\Modules\Workshop\Application\UseCases\CreateVehicle\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class CreateVehicleInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public string $plate,
        public string $brand,
        public string $model,
        public int $year,
        public string $ownerName,
        public string $ownerPhone,
    ) {}
}
