<?php

namespace App\Modules\Workshop\Domain\Entities;

use App\Modules\Inventory\Domain\ValueObjects\StockMovementId;
use App\Modules\Shared\Domain\Entities\Entity;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Validators\ServiceOrderStockMovementLinkValidator;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderItemId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderStockMovementLinkId;

final class ServiceOrderStockMovementLink extends Entity
{
    public function __construct(
        private readonly ServiceOrderStockMovementLinkId $id,
        private readonly TenantId $tenantId,
        private readonly ServiceOrderId $serviceOrderId,
        private readonly ServiceOrderItemId $serviceOrderItemId,
        private readonly StockMovementId $stockMovementId,
    ) {
        parent::__construct();

        ServiceOrderStockMovementLinkValidator::validate($this);

        $this->throwIfNotificationHasErrors();
    }

    public function id(): ServiceOrderStockMovementLinkId
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

    public function serviceOrderItemId(): ServiceOrderItemId
    {
        return $this->serviceOrderItemId;
    }

    public function stockMovementId(): StockMovementId
    {
        return $this->stockMovementId;
    }
}
