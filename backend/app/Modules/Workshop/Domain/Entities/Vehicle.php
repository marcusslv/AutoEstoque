<?php

namespace App\Modules\Workshop\Domain\Entities;

use App\Modules\Shared\Domain\Entities\Entity;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Validators\VehicleValidator;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use App\Modules\Workshop\Domain\ValueObjects\VehiclePlate;

final class Vehicle extends Entity
{
    public function __construct(
        private readonly VehicleId $id,
        private readonly TenantId $tenantId,
        private readonly VehiclePlate $plate,
        private readonly string $brand,
        private readonly string $model,
        private readonly int $year,
        private readonly string $ownerName,
        private readonly string $ownerPhone,
    ) {
        parent::__construct();

        VehicleValidator::validate($this);

        $this->throwIfNotificationHasErrors();
    }

    public function id(): VehicleId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function plate(): VehiclePlate
    {
        return $this->plate;
    }

    public function brand(): string
    {
        return $this->brand;
    }

    public function model(): string
    {
        return $this->model;
    }

    public function year(): int
    {
        return $this->year;
    }

    public function ownerName(): string
    {
        return $this->ownerName;
    }

    public function ownerPhone(): string
    {
        return $this->ownerPhone;
    }
}
