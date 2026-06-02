<?php

namespace App\Modules\Workshop\Domain\Exceptions;

use RuntimeException;

final class InvalidServiceOrderIdException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invalid service order id.');
    }
}
