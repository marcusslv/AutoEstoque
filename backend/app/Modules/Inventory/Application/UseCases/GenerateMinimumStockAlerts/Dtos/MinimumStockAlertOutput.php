<?php

namespace App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos;

final readonly class MinimumStockAlertOutput
{
    public function __construct(
        public string $productId,
        public string $productName,
        public string $productSku,
        public int $currentStock,
        public int $minimumStock,
        public int $shortageQuantity,
    ) {}
}
