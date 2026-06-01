<?php

namespace App\Modules\Inventory\Domain\Entities;

use App\Modules\Inventory\Domain\Exceptions\InsufficientStockException;
use App\Modules\Inventory\Domain\Validators\InventoryItemValidator;
use App\Modules\Inventory\Domain\ValueObjects\InventoryItemId;
use App\Modules\Inventory\Domain\ValueObjects\MovementQuantity;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Inventory\Domain\ValueObjects\StockQuantity;
use App\Modules\Shared\Domain\Entities\Entity;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

final class InventoryItem extends Entity
{
    public function __construct(
        private readonly InventoryItemId $id,
        private readonly TenantId $tenantId,
        private readonly StockProductId $productId,
        private StockQuantity $currentStock,
    ) {
        parent::__construct();

        InventoryItemValidator::validate($this);

        $this->throwIfNotificationHasErrors();
    }

    public function increase(MovementQuantity $quantity): void
    {
        $this->currentStock = $this->currentStock->add($quantity);

        InventoryItemValidator::validate($this);

        $this->throwIfNotificationHasErrors();
    }

    public function decrease(MovementQuantity $quantity): void
    {
        if ($quantity->value > $this->currentStock->value) {
            throw new InsufficientStockException;
        }

        $this->currentStock = $this->currentStock->subtract($quantity);

        InventoryItemValidator::validate($this);

        $this->throwIfNotificationHasErrors();
    }

    public function id(): InventoryItemId
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

    public function currentStock(): StockQuantity
    {
        return $this->currentStock;
    }
}
