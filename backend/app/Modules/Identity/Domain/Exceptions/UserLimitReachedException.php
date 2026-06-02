<?php

namespace App\Modules\Identity\Domain\Exceptions;

use RuntimeException;

final class UserLimitReachedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('User limit reached for this tenant.');
    }
}
