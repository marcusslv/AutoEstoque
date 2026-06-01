<?php

namespace App\Modules\Inventory\Application\UseCases\RegisterStockEntry\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class RegisterStockEntryOutput implements OutputDto
{
    public function __construct(
        public string $movementId,
        public string $inventoryItemId,
        public string $tenantId,
        public string $productId,
        public string $type,
        public int $quantity,
        public int $currentStock,
        public string $reason,
        public ?string $note,
        public ?int $unitCostInCents,
        public string $occurredAt,
    ) {}
}
