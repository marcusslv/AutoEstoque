<?php

namespace App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class ViewDashboardOutput implements OutputDto
{
    /**
     * @param  array<int, DashboardRecentMovementOutput>  $recentMovements
     */
    public function __construct(
        public string $tenantId,
        public string $date,
        public int $totalProducts,
        public int $productsBelowMinimum,
        public int $productsZeroStock,
        public int $totalStockValueInCents,
        public int $todayMovements,
        public array $recentMovements,
    ) {}
}
