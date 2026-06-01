<?php

namespace App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class ListStockMovementHistoryOutput implements OutputDto
{
    /**
     * @param  array<int, ListStockMovementHistoryItemOutput>  $items
     */
    public function __construct(public array $items) {}

    public function total(): int
    {
        return count($this->items);
    }
}
