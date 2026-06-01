<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

use App\Modules\Catalog\Domain\Exceptions\InvalidBarcodeException;

final readonly class Barcode
{
    public ?string $value;

    public function __construct(?string $value)
    {
        if ($value === null) {
            $this->value = null;

            return;
        }

        $value = trim($value);

        if ($value === '') {
            throw new InvalidBarcodeException;
        }

        $this->value = $value;
    }
}
