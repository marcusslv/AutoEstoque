<?php

namespace App\Modules\Workshop\Domain\Factories;

use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\Vehicle;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use App\Modules\Workshop\Domain\ValueObjects\VehiclePlate;

final class VehicleFactory
{
    public function create(
        VehicleId $id,
        TenantId $tenantId,
        VehiclePlate $plate,
        string $brand,
        string $model,
        int $year,
        string $ownerName,
        string $ownerPhone,
    ): Vehicle {
        return new Vehicle(
            id: $id,
            tenantId: $tenantId,
            plate: $plate,
            brand: trim($brand),
            model: trim($model),
            year: $year,
            ownerName: trim($ownerName),
            ownerPhone: trim($ownerPhone),
        );
    }
}
