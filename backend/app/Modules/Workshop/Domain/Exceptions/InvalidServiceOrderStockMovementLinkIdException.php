<?php

namespace App\Modules\Workshop\Domain\Exceptions;

use InvalidArgumentException;

final class InvalidServiceOrderStockMovementLinkIdException extends InvalidArgumentException
{
    protected $message = 'Invalid service order stock movement link id.';
}
