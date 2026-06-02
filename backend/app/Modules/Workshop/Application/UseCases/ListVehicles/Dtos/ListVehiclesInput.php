<?php

namespace App\Modules\Workshop\Application\UseCases\ListVehicles\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class ListVehiclesInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public ?string $term = null,
        public int $limit = 50,
    ) {}
}
