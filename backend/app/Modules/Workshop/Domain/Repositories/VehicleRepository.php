<?php

namespace App\Modules\Workshop\Domain\Repositories;

use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\Vehicle;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use App\Modules\Workshop\Domain\ValueObjects\VehiclePlate;

interface VehicleRepository
{
    public function findById(TenantId $tenantId, VehicleId $vehicleId): ?Vehicle;

    public function existsByPlate(TenantId $tenantId, VehiclePlate $plate): bool;

    public function save(Vehicle $vehicle): void;
}
