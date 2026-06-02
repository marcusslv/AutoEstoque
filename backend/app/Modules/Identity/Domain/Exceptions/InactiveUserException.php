<?php

namespace App\Modules\Identity\Domain\Exceptions;

use RuntimeException;

final class InactiveUserException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('User account is inactive.');
    }
}
