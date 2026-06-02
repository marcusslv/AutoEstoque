<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreateServiceOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_service_order(): void
    {
        $tenantId = $this->createTenant();
        $vehicleId = $this->createVehicle($tenantId);
        $userId = fake()->uuid();

        $response = $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => $userId,
        ])->postJson('/api/v1/service-orders', [
            'vehicle_id' => $vehicleId,
            'customer_name' => 'Joao Silva',
            'services_description' => 'Troca de oleo e filtros',
            'observations' => 'Cliente aguardando',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.tenant_id', $tenantId)
            ->assertJsonPath('data.vehicle_id', $vehicleId)
            ->assertJsonPath('data.created_by_user_id', $userId)
            ->assertJsonPath('data.customer_name', 'Joao Silva')
            ->assertJsonPath('data.services_description', 'Troca de oleo e filtros')
            ->assertJsonPath('data.observations', 'Cliente aguardando')
            ->assertJsonPath('data.status', 'open');

        $this->assertDatabaseHas('service_orders', [
            'tenant_id' => $tenantId,
            'vehicle_id' => $vehicleId,
            'created_by_user_id' => $userId,
            'customer_name' => 'Joao Silva',
            'services_description' => 'Troca de oleo e filtros',
            'observations' => 'Cliente aguardando',
            'status' => 'open',
        ]);
    }

    public function test_it_rejects_unknown_vehicle(): void
    {
        $tenantId = $this->createTenant();

        $response = $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => fake()->uuid(),
        ])->postJson('/api/v1/service-orders', $this->payload([
            'vehicle_id' => fake()->uuid(),
        ]));

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Vehicle not found.',
            ]);
    }

    public function test_it_rejects_vehicle_from_another_tenant(): void
    {
        $firstTenantId = $this->createTenant('Oficina A');
        $secondTenantId = $this->createTenant('Oficina B');
        $vehicleId = $this->createVehicle($firstTenantId);

        $response = $this->withHeaders([
            'X-Tenant-Id' => $secondTenantId,
            'X-User-Id' => fake()->uuid(),
        ])->postJson('/api/v1/service-orders', $this->payload([
            'vehicle_id' => $vehicleId,
        ]));

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Vehicle not found.',
            ]);
    }

    public function test_it_rejects_invalid_payload(): void
    {
        $tenantId = $this->createTenant();

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->postJson('/api/v1/service-orders', [
                'vehicle_id' => 'invalid',
                'customer_name' => '',
                'services_description' => '',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'X-User-Id',
                'vehicle_id',
                'customer_name',
                'services_description',
            ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'vehicle_id' => fake()->uuid(),
            'customer_name' => 'Joao Silva',
            'services_description' => 'Troca de oleo e filtros',
            'observations' => null,
        ], $overrides);
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

    private function createVehicle(string $tenantId): string
    {
        $vehicleId = fake()->uuid();

        DB::table('vehicles')->insert([
            'id' => $vehicleId,
            'tenant_id' => $tenantId,
            'plate' => strtoupper(fake()->bothify('???#?##')),
            'brand' => 'Chevrolet',
            'model' => 'Onix',
            'year' => 2020,
            'owner_name' => 'Joao Silva',
            'owner_phone' => '11999990000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $vehicleId;
    }
}
