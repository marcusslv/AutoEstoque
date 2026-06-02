<?php

namespace App\Modules\Workshop\Application\UseCases\FinishServiceOrder\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class FinishServiceOrderOutput implements OutputDto
{
    /**
     * @param  array<int, string>  $movementIds
     */
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $status,
        public string $finishedAt,
        public array $movementIds,
    ) {}
}
