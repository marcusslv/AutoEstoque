<?php

namespace App\Modules\Workshop\Domain\Exceptions;

use RuntimeException;

final class ServiceOrderNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Service order not found.');
    }
}
