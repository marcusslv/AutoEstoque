<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_product(): void
    {
        $tenantId = $this->createTenant();

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->postJson('/api/v1/products', [
                'name' => 'Filtro de oleo',
                'sku' => ' fo-001 ',
                'barcode' => '7891234567890',
                'category' => 'Filtros',
                'brand' => 'Mann',
                'supplier' => 'Auto Pecas Central',
                'minimum_stock' => 3,
                'cost_in_cents' => 2590,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.tenant_id', $tenantId)
            ->assertJsonPath('data.name', 'Filtro de oleo')
            ->assertJsonPath('data.sku', 'FO-001')
            ->assertJsonPath('data.barcode', '7891234567890')
            ->assertJsonPath('data.minimum_stock', 3)
            ->assertJsonPath('data.cost_in_cents', 2590)
            ->assertJsonPath('data.currency', 'BRL');

        $this->assertDatabaseHas('products', [
            'tenant_id' => $tenantId,
            'name' => 'Filtro de oleo',
            'sku' => 'FO-001',
            'barcode' => '7891234567890',
            'minimum_stock' => 3,
            'cost_in_cents' => 2590,
            'currency' => 'BRL',
        ]);
    }

    public function test_it_rejects_duplicated_sku_in_the_same_tenant(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId))
            ->postJson('/api/v1/products', $this->payload(['sku' => 'FO-001']))
            ->assertCreated();

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->postJson('/api/v1/products', $this->payload([
                'name' => 'Filtro de ar',
                'sku' => 'fo-001',
                'barcode' => '7890000000002',
            ]));

        $response->assertConflict()
            ->assertJson([
                'message' => 'SKU already exists for this tenant.',
            ]);
    }

    public function test_it_allows_same_sku_in_different_tenants(): void
    {
        $firstTenantId = $this->createTenant('Oficina A');
        $secondTenantId = $this->createTenant('Oficina B');

        $this->withHeaders($this->authHeaders($firstTenantId))
            ->postJson('/api/v1/products', $this->payload(['sku' => 'FO-001']))
            ->assertCreated();

        $this->withHeaders($this->authHeaders($secondTenantId))
            ->postJson('/api/v1/products', $this->payload(['sku' => 'FO-001']))
            ->assertCreated();
    }

    public function test_it_rejects_duplicated_barcode_in_the_same_tenant(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId))
            ->postJson('/api/v1/products', $this->payload([
                'sku' => 'FO-001',
                'barcode' => '7890000000001',
            ]))
            ->assertCreated();

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->postJson('/api/v1/products', $this->payload([
                'name' => 'Filtro de ar',
                'sku' => 'FA-001',
                'barcode' => '7890000000001',
            ]));

        $response->assertConflict()
            ->assertJson([
                'message' => 'Barcode already exists for this tenant.',
            ]);
    }

    public function test_it_rejects_invalid_payload(): void
    {
        $tenantId = $this->createTenant();

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->postJson('/api/v1/products', [
                'name' => '',
                'sku' => '',
                'cost_in_cents' => -1,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'sku',
                'cost_in_cents',
            ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Filtro de oleo',
            'sku' => 'FO-001',
            'barcode' => '7890000000001',
            'category' => 'Filtros',
            'brand' => 'Mann',
            'supplier' => 'Auto Pecas Central',
            'minimum_stock' => 2,
            'cost_in_cents' => 2590,
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
