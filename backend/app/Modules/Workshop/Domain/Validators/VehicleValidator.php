<?php

namespace App\Modules\Workshop\Domain\Validators;

use App\Modules\Workshop\Domain\Entities\Vehicle;

final class VehicleValidator
{
    public static function validate(Vehicle $vehicle): void
    {
        if (trim($vehicle->brand()) === '') {
            $vehicle->notification()->add(
                message: 'Vehicle brand is required.',
                field: 'brand',
                code: 'vehicle.brand_required',
            );
        }

        if (trim($vehicle->model()) === '') {
            $vehicle->notification()->add(
                message: 'Vehicle model is required.',
                field: 'model',
                code: 'vehicle.model_required',
            );
        }

        if ($vehicle->year() < 1900 || $vehicle->year() > ((int) date('Y') + 1)) {
            $vehicle->notification()->add(
                message: 'Vehicle year is invalid.',
                field: 'year',
                code: 'vehicle.year_invalid',
            );
        }

        if (trim($vehicle->ownerName()) === '') {
            $vehicle->notification()->add(
                message: 'Vehicle owner name is required.',
                field: 'owner_name',
                code: 'vehicle.owner_name_required',
            );
        }

        if (trim($vehicle->ownerPhone()) === '') {
            $vehicle->notification()->add(
                message: 'Vehicle owner phone is required.',
                field: 'owner_phone',
                code: 'vehicle.owner_phone_required',
            );
        }
    }
}
