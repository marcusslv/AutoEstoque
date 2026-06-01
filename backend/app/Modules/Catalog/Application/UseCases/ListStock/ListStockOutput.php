<?php

namespace App\Modules\Catalog\Application\UseCases\ListStock;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class ListStockOutput implements OutputDto
{
    /**
     * @param  array<int, ListStockItemOutput>  $items
     */
    public function __construct(public array $items) {}

    public function total(): int
    {
        return count($this->items);
    }
}
