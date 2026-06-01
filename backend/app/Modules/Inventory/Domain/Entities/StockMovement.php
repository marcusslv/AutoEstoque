<?php

namespace App\Modules\Inventory\Domain\Entities;

use App\Modules\Inventory\Domain\Validators\StockMovementValidator;
use App\Modules\Inventory\Domain\ValueObjects\MovementQuantity;
use App\Modules\Inventory\Domain\ValueObjects\MovementReason;
use App\Modules\Inventory\Domain\ValueObjects\StockEntryType;
use App\Modules\Inventory\Domain\ValueObjects\StockMovementId;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Shared\Domain\Entities\Entity;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use DateTimeImmutable;

final class StockMovement extends Entity
{
    public function __construct(
        private readonly StockMovementId $id,
        private readonly TenantId $tenantId,
        private readonly StockProductId $productId,
        private readonly string $userId,
        private readonly StockEntryType $type,
        private readonly MovementQuantity $quantity,
        private readonly MovementReason $reason,
        private readonly ?string $note,
        private readonly ?int $unitCostInCents,
        private readonly DateTimeImmutable $occurredAt,
    ) {
        parent::__construct();

        StockMovementValidator::validate($this);

        $this->throwIfNotificationHasErrors();
    }

    public function id(): StockMovementId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function productId(): StockProductId
    {
        return $this->productId;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function type(): StockEntryType
    {
        return $this->type;
    }

    public function quantity(): MovementQuantity
    {
        return $this->quantity;
    }

    public function reason(): MovementReason
    {
        return $this->reason;
    }

    public function note(): ?string
    {
        return $this->note;
    }

    public function unitCostInCents(): ?int
    {
        return $this->unitCostInCents;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
