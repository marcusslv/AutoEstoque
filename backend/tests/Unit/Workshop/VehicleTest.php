<?php

namespace Tests\Unit\Workshop;

use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\Vehicle;
use App\Modules\Workshop\Domain\Factories\VehicleFactory;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use App\Modules\Workshop\Domain\ValueObjects\VehiclePlate;
use PHPUnit\Framework\TestCase;

class VehicleTest extends TestCase
{
    public function test_it_creates_a_valid_vehicle(): void
    {
        $vehicle = $this->makeVehicle(plate: 'abc-1d23');

        $this->assertSame('ABC1D23', $vehicle->plate()->value);
        $this->assertSame('Chevrolet', $vehicle->brand());
        $this->assertSame('Onix', $vehicle->model());
        $this->assertSame(2020, $vehicle->year());
        $this->assertSame('Joao Silva', $vehicle->ownerName());
    }

    public function test_it_rejects_blank_required_fields_using_domain_validator(): void
    {
        try {
            $this->makeVehicle(brand: ' ', model: ' ', ownerName: ' ', ownerPhone: ' ');
            $this->fail('Expected domain validation exception.');
        } catch (DomainValidationException $exception) {
            $this->assertSame([
                [
                    'field' => 'brand',
                    'message' => 'Vehicle brand is required.',
                    'code' => 'vehicle.brand_required',
                ],
                [
                    'field' => 'model',
                    'message' => 'Vehicle model is required.',
                    'code' => 'vehicle.model_required',
                ],
                [
                    'field' => 'owner_name',
                    'message' => 'Vehicle owner name is required.',
                    'code' => 'vehicle.owner_name_required',
                ],
                [
                    'field' => 'owner_phone',
                    'message' => 'Vehicle owner phone is required.',
                    'code' => 'vehicle.owner_phone_required',
                ],
            ], $exception->errors());
        }
    }

    public function test_it_rejects_invalid_year_using_domain_validator(): void
    {
        try {
            $this->makeVehicle(year: 1899);
            $this->fail('Expected domain validation exception.');
        } catch (DomainValidationException $exception) {
            $this->assertSame([
                [
                    'field' => 'year',
                    'message' => 'Vehicle year is invalid.',
                    'code' => 'vehicle.year_invalid',
                ],
            ], $exception->errors());
        }
    }

    private function makeVehicle(
        string $plate = 'ABC1D23',
        string $brand = 'Chevrolet',
        string $model = 'Onix',
        int $year = 2020,
        string $ownerName = 'Joao Silva',
        string $ownerPhone = '11999990000',
    ): Vehicle {
        return (new VehicleFactory)->create(
            id: new VehicleId('018f95f2-0f08-7f85-9b31-2d833a1a2f41'),
            tenantId: new TenantId('018f95f2-0f08-7f85-9b31-2d833a1a2f42'),
            plate: new VehiclePlate($plate),
            brand: $brand,
            model: $model,
            year: $year,
            ownerName: $ownerName,
            ownerPhone: $ownerPhone,
        );
    }
}
