<?php

namespace App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos;

final readonly class ZeroStockAlertOutput
{
    public function __construct(
        public string $productId,
        public string $productName,
        public string $productSku,
        public int $currentStock,
        public int $minimumStock,
    ) {}
}
