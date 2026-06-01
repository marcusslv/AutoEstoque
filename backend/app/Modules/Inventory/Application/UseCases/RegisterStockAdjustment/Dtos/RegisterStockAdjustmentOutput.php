<?php

namespace App\Modules\Inventory\Application\UseCases\RegisterStockAdjustment\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class RegisterStockAdjustmentOutput implements OutputDto
{
    public function __construct(
        public string $movementId,
        public string $inventoryItemId,
        public string $tenantId,
        public string $productId,
        public string $direction,
        public string $type,
        public int $quantity,
        public int $currentStock,
        public string $reason,
        public ?string $note,
        public string $occurredAt,
    ) {}
}
