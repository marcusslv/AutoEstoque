<?php

namespace App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos;

final readonly class MostConsumedProductOutput
{
    public function __construct(
        public string $productId,
        public string $productName,
        public string $productSku,
        public int $totalQuantity,
        public int $movementsCount,
    ) {}
}
