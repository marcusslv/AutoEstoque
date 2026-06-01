<?php

namespace App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class GenerateZeroStockAlertsOutput implements OutputDto
{
    /**
     * @param  array<int, ZeroStockAlertOutput>  $alerts
     */
    public function __construct(public array $alerts) {}

    public function total(): int
    {
        return count($this->alerts);
    }
}
