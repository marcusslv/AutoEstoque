<?php

namespace App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos;

final readonly class ServiceOrderPartOutput
{
    /**
     * @param  array<int, ServiceOrderPartMovementOutput>  $movements
     */
    public function __construct(
        public string $id,
        public string $productId,
        public string $productName,
        public string $productSku,
        public string $addedByUserId,
        public int $quantity,
        public string $createdAt,
        public array $movements = [],
    ) {}

    public function movementsTotal(): int
    {
        return count($this->movements);
    }
}
