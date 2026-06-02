<?php

namespace App\Modules\Identity\Domain\Exceptions;

use RuntimeException;

final class UserNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('User not found for this tenant.');
    }
}
