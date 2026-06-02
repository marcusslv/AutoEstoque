<?php

namespace App\Modules\Identity\Domain\Exceptions;

use RuntimeException;

final class UserWithoutTenantException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('User is not linked to a tenant.');
    }
}
