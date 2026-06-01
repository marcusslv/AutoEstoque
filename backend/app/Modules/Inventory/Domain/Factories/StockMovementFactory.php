<?php

namespace App\Modules\Inventory\Domain\Factories;

use App\Modules\Inventory\Domain\Entities\StockMovement;
use App\Modules\Inventory\Domain\ValueObjects\MovementQuantity;
use App\Modules\Inventory\Domain\ValueObjects\MovementReason;
use App\Modules\Inventory\Domain\ValueObjects\StockEntryType;
use App\Modules\Inventory\Domain\ValueObjects\StockMovementId;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use DateTimeImmutable;

final class StockMovementFactory
{
    public function createEntry(
        StockMovementId $id,
        TenantId $tenantId,
        StockProductId $productId,
        string $userId,
        StockEntryType $type,
        MovementQuantity $quantity,
        MovementReason $reason,
        ?string $note,
        ?int $unitCostInCents,
        DateTimeImmutable $occurredAt,
    ): StockMovement {
        return new StockMovement(
            id: $id,
            tenantId: $tenantId,
            productId: $productId,
            userId: $userId,
            type: $type,
            quantity: $quantity,
            reason: $reason,
            note: $this->nullableTrim($note),
            unitCostInCents: $unitCostInCents,
            occurredAt: $occurredAt,
        );
    }

    private function nullableTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
