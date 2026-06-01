<?php

namespace App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class GenerateMinimumStockAlertsInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public int $limit = 50,
    ) {}
}
