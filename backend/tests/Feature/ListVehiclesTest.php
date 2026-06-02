<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ListVehiclesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_vehicles_for_current_tenant(): void
    {
        $tenantId = $this->createTenant('Oficina A');
        $otherTenantId = $this->createTenant('Oficina B');
        $this->createVehicle($tenantId, plate: 'ABC1D23', ownerName: 'Joao Silva');
        $this->createVehicle($tenantId, plate: 'XYZ9A88', ownerName: 'Maria Souza');
        $this->createVehicle($otherTenantId, plate: 'ZZZ1A11', ownerName: 'Outro Cliente');

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/vehicles');

        $response->assertOk()
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('data.0.plate', 'ABC1D23')
            ->assertJsonPath('data.1.plate', 'XYZ9A88');
    }

    public function test_it_filters_vehicles_by_search_term(): void
    {
        $tenantId = $this->createTenant();
        $this->createVehicle($tenantId, plate: 'ABC1D23', brand: 'Chevrolet', model: 'Onix', ownerName: 'Joao Silva');
        $this->createVehicle($tenantId, plate: 'XYZ9A88', brand: 'Fiat', model: 'Argo', ownerName: 'Maria Souza');

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/vehicles?search=argo');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.plate', 'XYZ9A88')
            ->assertJsonPath('data.0.model', 'Argo');
    }

    public function test_it_rejects_invalid_filters(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/vehicles?limit=0')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['limit']);
    }

    private function createTenant(string $name = 'Oficina Teste'): string
    {
        $tenantId = fake()->uuid();

        DB::table('tenants')->insert([
            'id' => $tenantId,
            'name' => $name,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $tenantId;
    }

    private function createVehicle(
        string $tenantId,
        string $plate,
        string $brand = 'Chevrolet',
        string $model = 'Onix',
        string $ownerName = 'Joao Silva',
    ): string {
        $vehicleId = fake()->uuid();

        DB::table('vehicles')->insert([
            'id' => $vehicleId,
            'tenant_id' => $tenantId,
            'plate' => $plate,
            'brand' => $brand,
            'model' => $model,
            'year' => 2020,
            'owner_name' => $ownerName,
            'owner_phone' => '11999990000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $vehicleId;
    }
}
