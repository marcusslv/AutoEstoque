<?php

namespace App\Modules\Workshop\Domain\Exceptions;

use RuntimeException;

final class InvalidVehicleIdException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invalid vehicle id.');
    }
}
