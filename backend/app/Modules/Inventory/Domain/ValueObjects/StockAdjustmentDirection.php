<?php

namespace App\Modules\Inventory\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class StockAdjustmentDirection
{
    private const ALLOWED = [
        'entry',
        'output',
    ];

    public function __construct(public string $value)
    {
        if (! in_array($this->value, self::ALLOWED, true)) {
            throw new InvalidArgumentException('Invalid stock adjustment direction.');
        }
    }

    public function isEntry(): bool
    {
        return $this->value === 'entry';
    }
}
