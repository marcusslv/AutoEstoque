<?php

namespace App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class ShowServiceOrderInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public string $serviceOrderId,
    ) {}
}
