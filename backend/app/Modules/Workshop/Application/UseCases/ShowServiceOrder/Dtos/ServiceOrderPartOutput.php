<?php

namespace App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos;

final readonly class ServiceOrderPartOutput
{
    public function __construct(
        public string $id,
        public string $productId,
        public string $productName,
        public string $productSku,
        public string $addedByUserId,
        public int $quantity,
        public string $createdAt,
    ) {}
}
