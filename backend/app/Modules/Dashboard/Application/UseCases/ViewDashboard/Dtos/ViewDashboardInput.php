<?php

namespace App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class ViewDashboardInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public string $date,
        public int $recentMovementsLimit = 5,
    ) {}
}
