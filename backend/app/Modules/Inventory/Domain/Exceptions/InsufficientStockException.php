<?php

namespace App\Modules\Inventory\Domain\Exceptions;

use RuntimeException;

final class InsufficientStockException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Insufficient stock for this operation.');
    }
}
