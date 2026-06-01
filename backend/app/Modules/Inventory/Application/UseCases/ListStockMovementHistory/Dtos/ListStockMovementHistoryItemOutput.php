<?php

namespace App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos;

final readonly class ListStockMovementHistoryItemOutput
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $productId,
        public string $productName,
        public string $productSku,
        public string $userId,
        public string $direction,
        public string $type,
        public int $quantity,
        public string $reason,
        public ?string $note,
        public ?int $unitCostInCents,
        public string $occurredAt,
    ) {}
}
