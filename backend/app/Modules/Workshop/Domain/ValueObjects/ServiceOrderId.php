<?php

namespace App\Modules\Workshop\Domain\ValueObjects;

use App\Modules\Workshop\Domain\Exceptions\InvalidServiceOrderIdException;
use Illuminate\Support\Str;

final readonly class ServiceOrderId
{
    public function __construct(public string $value)
    {
        if (! Str::isUuid($value)) {
            throw new InvalidServiceOrderIdException;
        }
    }
}
