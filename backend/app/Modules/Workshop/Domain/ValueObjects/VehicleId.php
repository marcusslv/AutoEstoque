<?php

namespace App\Modules\Workshop\Domain\ValueObjects;

use App\Modules\Workshop\Domain\Exceptions\InvalidVehicleIdException;
use Illuminate\Support\Str;

final readonly class VehicleId
{
    public function __construct(public string $value)
    {
        if (! Str::isUuid($value)) {
            throw new InvalidVehicleIdException;
        }
    }
}
