<?php

namespace App\Modules\Inventory\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class StockQuantity
{
    public function __construct(public int $value)
    {
        if ($this->value < 0) {
            throw new InvalidArgumentException('Stock quantity cannot be negative.');
        }
    }

    public function add(MovementQuantity $quantity): self
    {
        return new self($this->value + $quantity->value);
    }
}
