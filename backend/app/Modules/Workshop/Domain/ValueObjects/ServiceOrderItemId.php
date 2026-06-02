<?php

namespace App\Modules\Workshop\Domain\ValueObjects;

use App\Modules\Workshop\Domain\Exceptions\InvalidServiceOrderItemIdException;
use Illuminate\Support\Str;

final readonly class ServiceOrderItemId
{
    public function __construct(public string $value)
    {
        if (! Str::isUuid($value)) {
            throw new InvalidServiceOrderItemIdException;
        }
    }
}
