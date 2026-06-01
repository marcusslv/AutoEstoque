<?php

namespace App\Modules\Inventory\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class MovementQuantity
{
    public function __construct(public int $value)
    {
        if ($this->value <= 0) {
            throw new InvalidArgumentException('Movement quantity must be greater than zero.');
        }
    }
}
