<?php

namespace App\Modules\Workshop\Domain\Entities;

use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Shared\Domain\Entities\Entity;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Validators\ServiceOrderItemValidator;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderItemId;

final class ServiceOrderItem extends Entity
{
    public function __construct(
        private readonly ServiceOrderItemId $id,
        private readonly TenantId $tenantId,
        private readonly ServiceOrderId $serviceOrderId,
        private readonly ProductId $productId,
        private readonly string $addedByUserId,
        private readonly int $quantity,
    ) {
        parent::__construct();

        ServiceOrderItemValidator::validate($this);

        $this->throwIfNotificationHasErrors();
    }

    public function id(): ServiceOrderItemId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function serviceOrderId(): ServiceOrderId
    {
        return $this->serviceOrderId;
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function addedByUserId(): string
    {
        return $this->addedByUserId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}
