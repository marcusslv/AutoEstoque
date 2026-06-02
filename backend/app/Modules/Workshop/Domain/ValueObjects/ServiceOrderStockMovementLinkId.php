<?php

namespace App\Modules\Workshop\Domain\ValueObjects;

use App\Modules\Workshop\Domain\Exceptions\InvalidServiceOrderStockMovementLinkIdException;
use Illuminate\Support\Str;

final readonly class ServiceOrderStockMovementLinkId
{
    public function __construct(public string $value)
    {
        if (! Str::isUuid($value)) {
            throw new InvalidServiceOrderStockMovementLinkIdException;
        }
    }
}
