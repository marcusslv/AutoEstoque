<?php

namespace App\Modules\Workshop\Domain\Exceptions;

use RuntimeException;

final class InvalidVehiclePlateException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invalid vehicle plate.');
    }
}
