<?php

namespace App\Modules\Workshop\Domain\Exceptions;

use RuntimeException;

final class DuplicatedVehiclePlateException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Vehicle plate already exists for this tenant.');
    }
}
