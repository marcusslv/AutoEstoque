<?php

namespace App\Modules\Identity\Domain\Exceptions;

use RuntimeException;

final class DuplicatedUserEmailException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('User email already exists.');
    }
}
