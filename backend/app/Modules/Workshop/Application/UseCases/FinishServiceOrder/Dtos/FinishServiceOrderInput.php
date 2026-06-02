<?php

namespace App\Modules\Workshop\Application\UseCases\FinishServiceOrder\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class FinishServiceOrderInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public string $serviceOrderId,
        public string $finishedByUserId,
    ) {}
}
