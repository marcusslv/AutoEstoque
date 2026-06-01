<?php

namespace App\Modules\Tenant\Domain\ValueObjects;

use App\Modules\Tenant\Domain\Exceptions\InvalidTenantIdException;
use Illuminate\Support\Str;

final readonly class TenantId
{
    public function __construct(public string $value)
    {
        if (! Str::isUuid($value)) {
            throw new InvalidTenantIdException;
        }
    }

    public function equals(self $tenantId): bool
    {
        return $this->value === $tenantId->value;
    }
}
