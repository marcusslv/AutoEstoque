<?php

namespace App\Modules\Tenant\Domain\Exceptions;

use InvalidArgumentException;

final class InvalidTenantIdException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Tenant id must be a valid UUID.');
    }
}
