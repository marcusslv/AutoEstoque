<?php

namespace App\Modules\Catalog\Domain\Exceptions;

use InvalidArgumentException;

final class InvalidProductIdException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Product id must be a valid UUID.');
    }
}
