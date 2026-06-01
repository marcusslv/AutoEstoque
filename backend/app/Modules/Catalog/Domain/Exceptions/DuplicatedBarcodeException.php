<?php

namespace App\Modules\Catalog\Domain\Exceptions;

use RuntimeException;

final class DuplicatedBarcodeException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Barcode already exists for this tenant.');
    }
}
