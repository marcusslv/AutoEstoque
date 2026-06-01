<?php

namespace App\Modules\Catalog\Domain\Exceptions;

use InvalidArgumentException;

final class InvalidSkuException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('SKU is required.');
    }
}
