<?php

namespace App\Modules\Workshop\Domain\Exceptions;

use RuntimeException;

final class ServiceOrderHasNoItemsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Service order has no parts.');
    }
}
