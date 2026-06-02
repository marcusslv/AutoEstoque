<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreateVehicleTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_vehicle(): void
    {
        $tenantId = $this->createTenant();

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->postJson('/api/v1/vehicles', [
                'plate' => 'abc-1d23',
                'brand' => 'Chevrolet',
                'model' => 'Onix',
                'year' => 2020,
                'owner_name' => 'Joao Silva',
                'owner_phone' => '11999990000',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.tenant_id', $tenantId)
            ->assertJsonPath('data.plate', 'ABC1D23')
            ->assertJsonPath('data.brand', 'Chevrolet')
            ->assertJsonPath('data.model', 'Onix')
            ->assertJsonPath('data.year', 2020)
            ->assertJsonPath('data.owner_name', 'Joao Silva')
            ->assertJsonPath('data.owner_phone', '11999990000');

        $this->assertDatabaseHas('vehicles', [
            'tenant_id' => $tenantId,
            'plate' => 'ABC1D23',
            'brand' => 'Chevrolet',
            'model' => 'Onix',
            'year' => 2020,
            'owner_name' => 'Joao Silva',
            'owner_phone' => '11999990000',
        ]);
    }

    public function test_it_rejects_duplicated_plate_in_the_same_tenant(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeader('X-Tenant-Id', $tenantId)
            ->postJson('/api/v1/vehicles', $this->payload(['plate' => 'ABC1D23']))
            ->assertCreated();

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->postJson('/api/v1/vehicles', $this->payload(['plate' => 'abc-1d23']));

        $response->assertConflict()
            ->assertJson([
                'message' => 'Vehicle plate already exists for this tenant.',
            ]);
    }

    public function test_it_allows_same_plate_in_different_tenants(): void
    {
        $firstTenantId = $this->createTenant('Oficina A');
        $secondTenantId = $this->createTenant('Oficina B');

        $this->withHeader('X-Tenant-Id', $firstTenantId)
            ->postJson('/api/v1/vehicles', $this->payload(['plate' => 'ABC1D23']))
            ->assertCreated();

        $this->withHeader('X-Tenant-Id', $secondTenantId)
            ->postJson('/api/v1/vehicles', $this->payload(['plate' => 'abc-1d23']))
            ->assertCreated();
    }

    public function test_it_rejects_invalid_payload(): void
    {
        $tenantId = $this->createTenant();

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->postJson('/api/v1/vehicles', [
                'plate' => '',
                'brand' => '',
                'model' => '',
                'year' => 1899,
                'owner_name' => '',
                'owner_phone' => '',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'plate',
                'brand',
                'model',
                'year',
                'owner_name',
                'owner_phone',
            ]);
    }

    public function test_it_rejects_invalid_plate_format(): void
    {
        $tenantId = $this->createTenant();

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->postJson('/api/v1/vehicles', $this->payload(['plate' => 'ABC123']));

        $response->assertUnprocessable()
            ->assertJson([
                'message' => 'Invalid vehicle plate.',
            ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'plate' => 'ABC1D23',
            'brand' => 'Chevrolet',
            'model' => 'Onix',
            'year' => 2020,
            'owner_name' => 'Joao Silva',
            'owner_phone' => '11999990000',
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
}
