<?php

namespace App\Modules\Catalog\Domain\Exceptions;

use InvalidArgumentException;

final class InvalidBarcodeException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Barcode cannot be empty when provided.');
    }
}
