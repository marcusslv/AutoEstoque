<?php

namespace App\Modules\Workshop\Domain\ValueObjects;

use App\Modules\Workshop\Domain\Exceptions\InvalidVehiclePlateException;

final readonly class VehiclePlate
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $value) ?? '');

        if (! preg_match('/^[A-Z0-9]{7}$/', $this->value)) {
            throw new InvalidVehiclePlateException;
        }
    }
}
