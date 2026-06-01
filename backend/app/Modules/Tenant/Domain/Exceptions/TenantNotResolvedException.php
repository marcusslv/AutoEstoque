<?php

namespace App\Modules\Tenant\Domain\Exceptions;

use RuntimeException;

final class TenantNotResolvedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Tenant was not resolved for the current request.');
    }
}
