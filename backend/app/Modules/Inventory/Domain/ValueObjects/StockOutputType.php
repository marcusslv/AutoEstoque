<?php

namespace App\Modules\Inventory\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class StockOutputType
{
    private const ALLOWED = [
        'service_consumption',
        'loss',
        'breakage',
        'manual_adjustment',
    ];

    public function __construct(public string $value)
    {
        if (! in_array($this->value, self::ALLOWED, true)) {
            throw new InvalidArgumentException('Invalid stock output type.');
        }
    }
}
