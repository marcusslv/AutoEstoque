<?php

namespace App\Modules\Workshop\Domain\Exceptions;

use RuntimeException;

final class VehicleNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Vehicle not found.');
    }
}
