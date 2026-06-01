<?php

namespace App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class ListStockMovementHistoryInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public ?string $productId = null,
        public ?string $direction = null,
        public ?string $type = null,
        public ?string $userId = null,
        public ?string $occurredFrom = null,
        public ?string $occurredTo = null,
        public int $limit = 50,
    ) {}
}
