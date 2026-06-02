<?php

namespace App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class ShowServiceOrderOutput implements OutputDto
{
    /**
     * @param  array<int, ServiceOrderPartOutput>  $parts
     */
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $createdByUserId,
        public string $customerName,
        public string $servicesDescription,
        public ?string $observations,
        public string $status,
        public string $openedAt,
        public ?string $finishedAt,
        public ServiceOrderVehicleOutput $vehicle,
        public array $parts,
    ) {}

    public function partsTotal(): int
    {
        return count($this->parts);
    }
}
