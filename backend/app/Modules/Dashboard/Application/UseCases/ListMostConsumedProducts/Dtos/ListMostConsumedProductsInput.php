<?php

namespace App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class ListMostConsumedProductsInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public string $periodFrom,
        public string $periodTo,
        public int $limit = 10,
    ) {}
}
