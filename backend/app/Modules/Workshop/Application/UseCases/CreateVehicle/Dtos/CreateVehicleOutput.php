<?php

namespace App\Modules\Workshop\Application\UseCases\CreateVehicle\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class CreateVehicleOutput implements OutputDto
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $plate,
        public string $brand,
        public string $model,
        public int $year,
        public string $ownerName,
        public string $ownerPhone,
    ) {}
}
