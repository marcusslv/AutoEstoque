<?php

namespace Tests\Unit\Workshop;

use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Application\UseCases\CreateVehicle\CreateVehicleUseCase;
use App\Modules\Workshop\Application\UseCases\CreateVehicle\Dtos\CreateVehicleInput;
use App\Modules\Workshop\Application\UseCases\CreateVehicle\Dtos\CreateVehicleOutput;
use App\Modules\Workshop\Domain\Entities\Vehicle;
use App\Modules\Workshop\Domain\Exceptions\DuplicatedVehiclePlateException;
use App\Modules\Workshop\Domain\Factories\VehicleFactory;
use App\Modules\Workshop\Domain\Repositories\VehicleRepository;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use App\Modules\Workshop\Domain\ValueObjects\VehiclePlate;
use PHPUnit\Framework\TestCase;

class CreateVehicleUseCaseTest extends TestCase
{
    public function test_it_creates_a_vehicle(): void
    {
        $repository = new InMemoryVehicleRepository;
        $useCase = new CreateVehicleUseCase($repository, new VehicleFactory);

        $output = $useCase->execute($this->input(plate: 'abc-1d23'));

        $this->assertInstanceOf(CreateVehicleOutput::class, $output);
        $this->assertSame('018f95f2-0f08-7f85-9b31-2d833a1a2f42', $output->tenantId);
        $this->assertSame('ABC1D23', $output->plate);
        $this->assertSame('Chevrolet', $output->brand);
        $this->assertSame('Onix', $output->model);
        $this->assertSame(2020, $output->year);
        $this->assertCount(1, $repository->vehicles);
    }

    public function test_it_rejects_duplicated_plate_in_the_same_tenant(): void
    {
        $repository = new InMemoryVehicleRepository;
        $useCase = new CreateVehicleUseCase($repository, new VehicleFactory);

        $useCase->execute($this->input(plate: 'ABC1D23'));

        $this->expectException(DuplicatedVehiclePlateException::class);

        $useCase->execute($this->input(plate: 'abc-1d23'));
    }

    public function test_it_allows_same_plate_in_different_tenants(): void
    {
        $repository = new InMemoryVehicleRepository;
        $useCase = new CreateVehicleUseCase($repository, new VehicleFactory);

        $useCase->execute($this->input());
        $useCase->execute($this->input(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f43',
        ));

        $this->assertCount(2, $repository->vehicles);
    }

    private function input(
        string $tenantId = '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
        string $plate = 'ABC1D23',
    ): CreateVehicleInput {
        return new CreateVehicleInput(
            tenantId: $tenantId,
            plate: $plate,
            brand: 'Chevrolet',
            model: 'Onix',
            year: 2020,
            ownerName: 'Joao Silva',
            ownerPhone: '11999990000',
        );
    }
}

final class InMemoryVehicleRepository implements VehicleRepository
{
    /**
     * @var array<int, Vehicle>
     */
    public array $vehicles = [];

    public function findById(TenantId $tenantId, VehicleId $vehicleId): ?Vehicle
    {
        foreach ($this->vehicles as $vehicle) {
            if ($vehicle->tenantId()->equals($tenantId) && $vehicle->id()->value === $vehicleId->value) {
                return $vehicle;
            }
        }

        return null;
    }

    public function existsByPlate(TenantId $tenantId, VehiclePlate $plate): bool
    {
        foreach ($this->vehicles as $vehicle) {
            if ($vehicle->tenantId()->equals($tenantId) && $vehicle->plate()->value === $plate->value) {
                return true;
            }
        }

        return false;
    }

    public function save(Vehicle $vehicle): void
    {
        $this->vehicles[] = $vehicle;
    }
}
