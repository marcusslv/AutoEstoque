<?php

namespace Tests\Unit\Workshop;

use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Application\UseCases\CreateServiceOrder\CreateServiceOrderUseCase;
use App\Modules\Workshop\Application\UseCases\CreateServiceOrder\Dtos\CreateServiceOrderInput;
use App\Modules\Workshop\Application\UseCases\CreateServiceOrder\Dtos\CreateServiceOrderOutput;
use App\Modules\Workshop\Domain\Entities\ServiceOrder;
use App\Modules\Workshop\Domain\Entities\Vehicle;
use App\Modules\Workshop\Domain\Exceptions\VehicleNotFoundException;
use App\Modules\Workshop\Domain\Factories\ServiceOrderFactory;
use App\Modules\Workshop\Domain\Factories\VehicleFactory;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderRepository;
use App\Modules\Workshop\Domain\Repositories\VehicleRepository;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use App\Modules\Workshop\Domain\ValueObjects\VehiclePlate;
use PHPUnit\Framework\TestCase;

class CreateServiceOrderUseCaseTest extends TestCase
{
    public function test_it_creates_a_service_order(): void
    {
        $vehicles = new InMemoryServiceOrderVehicleRepository;
        $vehicles->save($this->vehicle());

        $serviceOrders = new InMemoryServiceOrderRepository;
        $useCase = new CreateServiceOrderUseCase($vehicles, $serviceOrders, new ServiceOrderFactory);

        $output = $useCase->execute($this->input());

        $this->assertInstanceOf(CreateServiceOrderOutput::class, $output);
        $this->assertSame('018f95f2-0f08-7f85-9b31-2d833a1a2f42', $output->tenantId);
        $this->assertSame('018f95f2-0f08-7f85-9b31-2d833a1a2f43', $output->vehicleId);
        $this->assertSame('018f95f2-0f08-7f85-9b31-2d833a1a2f44', $output->createdByUserId);
        $this->assertSame('Joao Silva', $output->customerName);
        $this->assertSame('Troca de oleo e filtros', $output->servicesDescription);
        $this->assertSame('open', $output->status);
        $this->assertCount(1, $serviceOrders->serviceOrders);
    }

    public function test_it_rejects_vehicle_from_another_tenant(): void
    {
        $vehicles = new InMemoryServiceOrderVehicleRepository;
        $vehicles->save($this->vehicle(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f45',
        ));

        $useCase = new CreateServiceOrderUseCase(
            $vehicles,
            new InMemoryServiceOrderRepository,
            new ServiceOrderFactory,
        );

        $this->expectException(VehicleNotFoundException::class);

        $useCase->execute($this->input());
    }

    private function input(): CreateServiceOrderInput
    {
        return new CreateServiceOrderInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            vehicleId: '018f95f2-0f08-7f85-9b31-2d833a1a2f43',
            createdByUserId: '018f95f2-0f08-7f85-9b31-2d833a1a2f44',
            customerName: 'Joao Silva',
            servicesDescription: 'Troca de oleo e filtros',
            observations: null,
        );
    }

    private function vehicle(
        string $tenantId = '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
    ): Vehicle {
        return (new VehicleFactory)->create(
            id: new VehicleId('018f95f2-0f08-7f85-9b31-2d833a1a2f43'),
            tenantId: new TenantId($tenantId),
            plate: new VehiclePlate('ABC1D23'),
            brand: 'Chevrolet',
            model: 'Onix',
            year: 2020,
            ownerName: 'Joao Silva',
            ownerPhone: '11999990000',
        );
    }
}

final class InMemoryServiceOrderVehicleRepository implements VehicleRepository
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

final class InMemoryServiceOrderRepository implements ServiceOrderRepository
{
    /**
     * @var array<int, ServiceOrder>
     */
    public array $serviceOrders = [];

    public function save(ServiceOrder $serviceOrder): void
    {
        $this->serviceOrders[] = $serviceOrder;
    }
}
