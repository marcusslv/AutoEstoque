<?php

namespace App\Modules\Workshop\Application\UseCases\CreateServiceOrder\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class CreateServiceOrderInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public string $vehicleId,
        public string $createdByUserId,
        public string $customerName,
        public string $servicesDescription,
        public ?string $observations,
    ) {}
}
