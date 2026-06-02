<?php

namespace App\Modules\Identity\Domain\Exceptions;

use RuntimeException;

final class AuthenticatedUserNotResolvedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Authenticated user was not resolved.');
    }
}
