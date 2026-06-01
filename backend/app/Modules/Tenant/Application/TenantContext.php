<?php

namespace App\Modules\Tenant\Application;

use App\Modules\Tenant\Domain\Exceptions\TenantNotResolvedException;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

final class TenantContext
{
    private ?TenantId $tenantId = null;

    public function set(TenantId $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    public function id(): TenantId
    {
        if ($this->tenantId === null) {
            throw new TenantNotResolvedException;
        }

        return $this->tenantId;
    }

    public function hasTenant(): bool
    {
        return $this->tenantId !== null;
    }

    public function clear(): void
    {
        $this->tenantId = null;
    }
}
