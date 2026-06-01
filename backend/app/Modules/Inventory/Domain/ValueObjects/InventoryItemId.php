<?php

namespace App\Modules\Inventory\Domain\ValueObjects;

use App\Modules\Catalog\Domain\Exceptions\InvalidProductIdException;
use Illuminate\Support\Str;

final readonly class InventoryItemId
{
    public function __construct(public string $value)
    {
        if (! Str::isUuid($value)) {
            throw new InvalidProductIdException;
        }
    }
}
