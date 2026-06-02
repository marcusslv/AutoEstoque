<?php

namespace App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class ListServiceOrdersInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public ?string $status = null,
        public ?string $term = null,
        public ?string $openedFrom = null,
        public ?string $openedTo = null,
        public int $limit = 50,
    ) {}
}
