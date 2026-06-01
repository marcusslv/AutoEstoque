<?php

namespace App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos;

final readonly class DashboardRecentMovementOutput
{
    public function __construct(
        public string $id,
        public string $productId,
        public string $productName,
        public string $productSku,
        public string $direction,
        public string $type,
        public int $quantity,
        public string $reason,
        public string $occurredAt,
    ) {}
}
