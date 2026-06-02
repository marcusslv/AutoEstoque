<?php

namespace App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\Vehicle;
use App\Modules\Workshop\Domain\Factories\VehicleFactory;
use App\Modules\Workshop\Domain\Repositories\VehicleRepository;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use App\Modules\Workshop\Domain\ValueObjects\VehiclePlate;
use App\Modules\Workshop\Infrastructure\Persistence\Eloquent\Models\VehicleModel;

final class EloquentVehicleRepository implements VehicleRepository
{
    public function __construct(private readonly VehicleFactory $vehicleFactory) {}

    public function findById(TenantId $tenantId, VehicleId $vehicleId): ?Vehicle
    {
        $model = VehicleModel::query()
            ->where('tenant_id', $tenantId->value)
            ->where('id', $vehicleId->value)
            ->first();

        if (! $model instanceof VehicleModel) {
            return null;
        }

        return $this->toDomain($model);
    }

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

    private function toDomain(VehicleModel $model): Vehicle
    {
        return $this->vehicleFactory->create(
            id: new VehicleId((string) $model->id),
            tenantId: new TenantId((string) $model->tenant_id),
            plate: new VehiclePlate((string) $model->plate),
            brand: (string) $model->brand,
            model: (string) $model->model,
            year: (int) $model->year,
            ownerName: (string) $model->owner_name,
            ownerPhone: (string) $model->owner_phone,
        );
    }
}
