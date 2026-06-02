<?php

namespace App\Modules\Workshop\Application\UseCases\CreateServiceOrder\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class CreateServiceOrderOutput implements OutputDto
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $vehicleId,
        public string $createdByUserId,
        public string $customerName,
        public string $servicesDescription,
        public ?string $observations,
        public string $status,
        public string $openedAt,
    ) {}
}
