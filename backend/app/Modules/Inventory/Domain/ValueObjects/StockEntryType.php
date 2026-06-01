<?php

namespace App\Modules\Inventory\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class StockEntryType
{
    private const ALLOWED = [
        'purchase',
        'manual_adjustment',
        'return',
    ];

    public function __construct(public string $value)
    {
        if (! in_array($this->value, self::ALLOWED, true)) {
            throw new InvalidArgumentException('Invalid stock entry type.');
        }
    }
}
