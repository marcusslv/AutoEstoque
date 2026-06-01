<?php

namespace App\Modules\Catalog\Domain\Exceptions;

use RuntimeException;

final class ProductNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Product not found for this tenant.');
    }
}
