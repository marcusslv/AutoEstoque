<?php

namespace App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos;

final readonly class StockMovementServiceOrderOutput
{
    public function __construct(
        public string $id,
        public string $itemId,
    ) {}
}
