<?php

namespace App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class ListMostConsumedProductsOutput implements OutputDto
{
    /**
     * @param  array<int, MostConsumedProductOutput>  $items
     */
    public function __construct(
        public string $tenantId,
        public string $periodFrom,
        public string $periodTo,
        public array $items,
    ) {}

    public function total(): int
    {
        return count($this->items);
    }
}
