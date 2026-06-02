<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\Vehicle;
use App\Modules\Workshop\Domain\Repositories\VehicleRepository;
use App\Modules\Workshop\Domain\ValueObjects\VehiclePlate;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models\VehicleModel;

final class EloquentVehicleRepository implements VehicleRepository
{
    public function existsByPlate(TenantId $tenantId, VehiclePlate $plate): bool
    {
        return VehicleModel::query()
            ->where('tenant_id', $tenantId->value)
            ->where('plate', $plate->value)
            ->exists();
    }

    public function save(Vehicle $vehicle): void
    {
        VehicleModel::query()->create([
            'id' => $vehicle->id()->value,
            'tenant_id' => $vehicle->tenantId()->value,
            'plate' => $vehicle->plate()->value,
            'brand' => $vehicle->brand(),
            'model' => $vehicle->model(),
            'year' => $vehicle->year(),
            'owner_name' => $vehicle->ownerName(),
            'owner_phone' => $vehicle->ownerPhone(),
        ]);
    }
}
