<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

use App\Modules\Catalog\Domain\Exceptions\InvalidProductIdException;
use Illuminate\Support\Str;

final readonly class ProductId
{
    public function __construct(public string $value)
    {
        if (! Str::isUuid($value)) {
            throw new InvalidProductIdException;
        }
    }
}
