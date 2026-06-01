<?php

namespace App\Modules\Inventory\Application\UseCases\RegisterStockAdjustment;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class RegisterStockAdjustmentInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public string $userId,
        public string $productId,
        public string $direction,
        public int $quantity,
        public string $reason,
        public ?string $note = null,
    ) {}
}
