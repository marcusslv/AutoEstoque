<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_product(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId);

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->patchJson("/api/v1/products/{$productId}", [
                'name' => 'Filtro de oleo atualizado',
                'sku' => ' fo-002 ',
                'barcode' => '7891234567899',
                'category' => 'Filtros atualizados',
                'brand' => 'Mahle',
                'supplier' => 'Auto Pecas Sul',
                'minimum_stock' => 5,
                'cost_in_cents' => 3190,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.id', $productId)
            ->assertJsonPath('data.tenant_id', $tenantId)
            ->assertJsonPath('data.name', 'Filtro de oleo atualizado')
            ->assertJsonPath('data.sku', 'FO-002')
            ->assertJsonPath('data.barcode', '7891234567899')
            ->assertJsonPath('data.minimum_stock', 5)
            ->assertJsonPath('data.cost_in_cents', 3190);

        $this->assertDatabaseHas('products', [
            'id' => $productId,
            'tenant_id' => $tenantId,
            'name' => 'Filtro de oleo atualizado',
            'sku' => 'FO-002',
            'barcode' => '7891234567899',
            'minimum_stock' => 5,
            'cost_in_cents' => 3190,
        ]);
    }

    public function test_it_allows_updating_product_keeping_same_sku_and_barcode(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId, sku: 'FO-001', barcode: '7891234567890');

        $this->withHeaders($this->authHeaders($tenantId))
            ->patchJson("/api/v1/products/{$productId}", $this->payload([
                'name' => 'Filtro atualizado',
                'sku' => 'FO-001',
                'barcode' => '7891234567890',
            ]))
            ->assertOk()
            ->assertJsonPath('data.name', 'Filtro atualizado');
    }

    public function test_it_rejects_product_from_another_tenant(): void
    {
        $firstTenantId = $this->createTenant('Oficina A');
        $secondTenantId = $this->createTenant('Oficina B');
        $productId = $this->createProduct($firstTenantId);

        $this->withHeaders($this->authHeaders($secondTenantId))
            ->patchJson("/api/v1/products/{$productId}", $this->payload())
            ->assertNotFound()
            ->assertJson([
                'message' => 'Product not found for this tenant.',
            ]);
    }

    public function test_it_rejects_duplicated_sku_in_the_same_tenant(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId, sku: 'FO-001', barcode: '7891234567890');
        $this->createProduct($tenantId, sku: 'FA-001', barcode: '7891234567891');

        $this->withHeaders($this->authHeaders($tenantId))
            ->patchJson("/api/v1/products/{$productId}", $this->payload([
                'sku' => 'FA-001',
                'barcode' => '7891234567892',
            ]))
            ->assertConflict()
            ->assertJson([
                'message' => 'SKU already exists for this tenant.',
            ]);
    }

    public function test_it_rejects_duplicated_barcode_in_the_same_tenant(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId, sku: 'FO-001', barcode: '7891234567890');
        $this->createProduct($tenantId, sku: 'FA-001', barcode: '7891234567891');

        $this->withHeaders($this->authHeaders($tenantId))
            ->patchJson("/api/v1/products/{$productId}", $this->payload([
                'sku' => 'FO-002',
                'barcode' => '7891234567891',
            ]))
            ->assertConflict()
            ->assertJson([
                'message' => 'Barcode already exists for this tenant.',
            ]);
    }

    public function test_it_rejects_invalid_payload(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId);

        $this->withHeaders($this->authHeaders($tenantId))
            ->patchJson("/api/v1/products/{$productId}", [
                'name' => '',
                'sku' => '',
                'cost_in_cents' => -1,
            ])
            ->assertUnprocessable()
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
            'barcode' => '7891234567890',
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

    private function createProduct(
        string $tenantId,
        string $sku = 'FO-001',
        ?string $barcode = '7891234567890',
    ): string {
        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->postJson('/api/v1/products', $this->payload([
                'sku' => $sku,
                'barcode' => $barcode,
            ]));

        $response->assertCreated();

        return (string) $response->json('data.id');
    }
}
