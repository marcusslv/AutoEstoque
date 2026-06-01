<?php

namespace App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class GenerateZeroStockAlertsInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public int $limit = 50,
    ) {}
}
