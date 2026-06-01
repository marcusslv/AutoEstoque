<?php

namespace App\Modules\Inventory\Application\UseCases\RegisterStockEntry\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class RegisterStockEntryInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public string $userId,
        public string $productId,
        public string $type,
        public int $quantity,
        public string $reason,
        public ?string $note = null,
        public ?int $unitCostInCents = null,
    ) {}
}
