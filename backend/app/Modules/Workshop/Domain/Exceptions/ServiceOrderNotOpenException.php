<?php

namespace App\Modules\Workshop\Domain\Exceptions;

use RuntimeException;

final class ServiceOrderNotOpenException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Service order is not open.');
    }
}
