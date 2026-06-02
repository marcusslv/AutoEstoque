<?php

namespace App\Modules\Workshop\Application\UseCases\CreateVehicle;

use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Application\UseCases\CreateVehicle\Dtos\CreateVehicleInput;
use App\Modules\Workshop\Application\UseCases\CreateVehicle\Dtos\CreateVehicleOutput;
use App\Modules\Workshop\Domain\Exceptions\DuplicatedVehiclePlateException;
use App\Modules\Workshop\Domain\Factories\VehicleFactory;
use App\Modules\Workshop\Domain\Repositories\VehicleRepository;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use App\Modules\Workshop\Domain\ValueObjects\VehiclePlate;
use Illuminate\Support\Str;

/**
 * @implements UseCase<CreateVehicleInput, CreateVehicleOutput>
 */
final readonly class CreateVehicleUseCase implements UseCase
{
    public function __construct(
        private VehicleRepository $vehicles,
        private VehicleFactory $vehicleFactory,
    ) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof CreateVehicleInput);

        $tenantId = new TenantId($input->tenantId);
        $plate = new VehiclePlate($input->plate);

        if ($this->vehicles->existsByPlate($tenantId, $plate)) {
            throw new DuplicatedVehiclePlateException;
        }

        $vehicle = $this->vehicleFactory->create(
            id: new VehicleId((string) Str::uuid()),
            tenantId: $tenantId,
            plate: $plate,
            brand: $input->brand,
            model: $input->model,
            year: $input->year,
            ownerName: $input->ownerName,
            ownerPhone: $input->ownerPhone,
        );

        $this->vehicles->save($vehicle);

        return new CreateVehicleOutput(
            id: $vehicle->id()->value,
            tenantId: $vehicle->tenantId()->value,
            plate: $vehicle->plate()->value,
            brand: $vehicle->brand(),
            model: $vehicle->model(),
            year: $vehicle->year(),
            ownerName: $vehicle->ownerName(),
            ownerPhone: $vehicle->ownerPhone(),
        );
    }
}
