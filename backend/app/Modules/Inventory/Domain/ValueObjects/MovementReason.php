<?php

namespace App\Modules\Inventory\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class MovementReason
{
    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('Movement reason is required.');
        }

        $this->value = $value;
    }
}
