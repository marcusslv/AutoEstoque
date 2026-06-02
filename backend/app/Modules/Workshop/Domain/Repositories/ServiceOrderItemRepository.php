<?php

namespace App\Modules\Workshop\Domain\Repositories;

use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\ServiceOrderItem;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;

interface ServiceOrderItemRepository
{
    /**
     * @return array<int, ServiceOrderItem>
     */
    public function listByServiceOrder(TenantId $tenantId, ServiceOrderId $serviceOrderId): array;

    public function save(ServiceOrderItem $item): void;
}
