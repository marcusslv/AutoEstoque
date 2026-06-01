<?php

namespace App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Contracts;

use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos\ListStockMovementHistoryInput;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos\ListStockMovementHistoryItemOutput;

interface StockMovementHistoryQuery
{
    /**
     * @return array<int, ListStockMovementHistoryItemOutput>
     */
    public function search(ListStockMovementHistoryInput $input): array;
}
