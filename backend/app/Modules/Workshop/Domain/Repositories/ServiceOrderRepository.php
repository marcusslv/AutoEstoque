<?php

namespace App\Modules\Workshop\Domain\Repositories;

use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\ServiceOrder;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;

interface ServiceOrderRepository
{
    public function findById(TenantId $tenantId, ServiceOrderId $serviceOrderId): ?ServiceOrder;

    public function save(ServiceOrder $serviceOrder): void;

    public function update(ServiceOrder $serviceOrder): void;
}
