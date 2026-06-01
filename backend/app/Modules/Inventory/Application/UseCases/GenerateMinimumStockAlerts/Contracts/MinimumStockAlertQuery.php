<?php

namespace App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Contracts;

use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos\GenerateMinimumStockAlertsInput;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos\MinimumStockAlertOutput;

interface MinimumStockAlertQuery
{
    /**
     * @return array<int, MinimumStockAlertOutput>
     */
    public function search(GenerateMinimumStockAlertsInput $input): array;
}
