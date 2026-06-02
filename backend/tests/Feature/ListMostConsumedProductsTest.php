<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ListMostConsumedProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_most_consumed_products_for_current_tenant(): void
    {
        $tenantId = $this->createTenant('Oficina A');
        $otherTenantId = $this->createTenant('Oficina B');
        $firstProductId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001');
        $secondProductId = $this->createProduct($tenantId, name: 'Pastilha de freio', sku: 'PF-001', barcode: '7891234567891');
        $thirdProductId = $this->createProduct($tenantId, name: 'Correia dentada', sku: 'CD-001', barcode: '7891234567892');
        $otherProductId = $this->createProduct($otherTenantId, name: 'Bateria', sku: 'BT-001', barcode: '7891234567893');
        $userId = fake()->uuid();

        $this->registerEntry($tenantId, $userId, $firstProductId, 10);
        $this->registerOutput($tenantId, $userId, $firstProductId, 3);
        $this->registerOutput($tenantId, $userId, $firstProductId, 2);

        $this->registerEntry($tenantId, $userId, $secondProductId, 10);
        $this->registerOutput($tenantId, $userId, $secondProductId, 4);

        $this->registerEntry($tenantId, $userId, $thirdProductId, 10);

        $this->registerEntry($otherTenantId, fake()->uuid(), $otherProductId, 10);
        $this->registerOutput($otherTenantId, fake()->uuid(), $otherProductId, 9);

        $today = now()->toDateString();
        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->getJson('/api/v1/dashboard/most-consumed-products?period_from='.$today.'&period_to='.$today);

        $response->assertOk()
            ->assertJsonPath('meta.tenant_id', $tenantId)
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('data.0.product_id', $firstProductId)
            ->assertJsonPath('data.0.total_quantity', 5)
            ->assertJsonPath('data.0.movements_count', 2)
            ->assertJsonPath('data.1.product_id', $secondProductId)
            ->assertJsonPath('data.1.total_quantity', 4)
            ->assertJsonPath('data.1.movements_count', 1);
    }

    public function test_it_limits_ranking_results(): void
    {
        $tenantId = $this->createTenant();
        $firstProductId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001');
        $secondProductId = $this->createProduct($tenantId, name: 'Pastilha de freio', sku: 'PF-001', barcode: '7891234567891');
        $userId = fake()->uuid();

        $this->registerEntry($tenantId, $userId, $firstProductId, 10);
        $this->registerOutput($tenantId, $userId, $firstProductId, 5);
        $this->registerEntry($tenantId, $userId, $secondProductId, 10);
        $this->registerOutput($tenantId, $userId, $secondProductId, 4);

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->getJson('/api/v1/dashboard/most-consumed-products?limit=1');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.product_id', $firstProductId);
    }

    public function test_it_returns_empty_state_when_period_has_no_outputs(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001');

        $this->registerEntry($tenantId, fake()->uuid(), $productId, 3);

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->getJson('/api/v1/dashboard/most-consumed-products');

        $response->assertOk()
            ->assertJsonPath('meta.total', 0)
            ->assertJsonPath('data', []);
    }

    public function test_it_rejects_invalid_filters(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeader('X-Tenant-Id', $tenantId)
            ->getJson('/api/v1/dashboard/most-consumed-products?period_from=2026-06-02&period_to=2026-06-01&limit=101')
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'period_to',
                'limit',
            ]);
    }

    private function registerEntry(string $tenantId, string $userId, string $productId, int $quantity): void
    {
        $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => $userId,
        ])->postJson('/api/v1/inventory/entries', [
            'product_id' => $productId,
            'type' => 'purchase',
            'quantity' => $quantity,
            'reason' => 'Compra de reposicao',
        ])->assertCreated();
    }

    private function registerOutput(string $tenantId, string $userId, string $productId, int $quantity): void
    {
        $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => $userId,
        ])->postJson('/api/v1/inventory/outputs', [
            'product_id' => $productId,
            'type' => 'service_consumption',
            'quantity' => $quantity,
            'reason' => 'Consumo em servico',
        ])->assertCreated();
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
        string $name,
        string $sku,
        ?string $barcode = '7891234567890',
    ): string {
        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->postJson('/api/v1/products', [
                'name' => $name,
                'sku' => $sku,
                'barcode' => $barcode,
                'category' => 'Filtros',
                'brand' => 'Mann',
                'supplier' => 'Auto Pecas Central',
                'minimum_stock' => 2,
                'cost_in_cents' => 2590,
            ]);

        $response->assertCreated();

        return (string) $response->json('data.id');
    }
}
