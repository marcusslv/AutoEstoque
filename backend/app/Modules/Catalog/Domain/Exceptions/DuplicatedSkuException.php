<?php

namespace App\Modules\Catalog\Domain\Exceptions;

use RuntimeException;

final class DuplicatedSkuException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('SKU already exists for this tenant.');
    }
}
