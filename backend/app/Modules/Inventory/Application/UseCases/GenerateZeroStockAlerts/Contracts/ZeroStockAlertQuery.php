<?php

namespace App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Contracts;

use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos\GenerateZeroStockAlertsInput;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos\ZeroStockAlertOutput;

interface ZeroStockAlertQuery
{
    /**
     * @return array<int, ZeroStockAlertOutput>
     */
    public function search(GenerateZeroStockAlertsInput $input): array;
}
