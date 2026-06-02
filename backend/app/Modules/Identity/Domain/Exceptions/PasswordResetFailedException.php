<?php

namespace App\Modules\Identity\Domain\Exceptions;

use RuntimeException;

final class PasswordResetFailedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Unable to reset password with the provided token.');
    }
}
