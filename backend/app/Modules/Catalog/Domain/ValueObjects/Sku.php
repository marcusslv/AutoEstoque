<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

use App\Modules\Catalog\Domain\Exceptions\InvalidSkuException;

final readonly class Sku
{
    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidSkuException;
        }

        $this->value = mb_strtoupper($value);
    }
}
