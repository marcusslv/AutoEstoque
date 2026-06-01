<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ListStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_products_for_current_tenant(): void
    {
        $tenantId = $this->createTenant('Oficina A');
        $otherTenantId = $this->createTenant('Oficina B');

        $productId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001');
        $this->registerEntry($tenantId, $productId, 5);
        $this->registerOutput($tenantId, $productId, 2);
        $this->createProduct($otherTenantId, name: 'Pastilha de freio', sku: 'PF-001', barcode: '7891234567891');

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->getJson('/api/v1/stock');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Filtro de oleo')
            ->assertJsonPath('data.0.sku', 'FO-001')
            ->assertJsonPath('data.0.current_stock', 3)
            ->assertJsonPath('data.0.stock_status', 'available');
    }

    public function test_it_filters_stock_by_search_term(): void
    {
        $tenantId = $this->createTenant();

        $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001', category: 'Filtros', brand: 'Mann');
        $this->createProduct($tenantId, name: 'Pastilha de freio', sku: 'PF-001', barcode: '7891234567891', category: 'Freios', brand: 'Bosch');

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->getJson('/api/v1/stock?search=bosch');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Pastilha de freio')
            ->assertJsonPath('data.0.brand', 'Bosch');
    }

    public function test_it_returns_empty_state_when_no_product_matches_search(): void
    {
        $tenantId = $this->createTenant();

        $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001');

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->getJson('/api/v1/stock?search=inexistente');

        $response->assertOk()
            ->assertJsonPath('meta.total', 0)
            ->assertJsonPath('data', []);
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
        ?string $category = 'Filtros',
        ?string $brand = 'Mann',
    ): string {
        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->postJson('/api/v1/products', [
                'name' => $name,
                'sku' => $sku,
                'barcode' => $barcode,
                'category' => $category,
                'brand' => $brand,
                'supplier' => 'Auto Pecas Central',
                'minimum_stock' => 2,
                'cost_in_cents' => 2590,
            ]);

        $response->assertCreated();

        return (string) $response->json('data.id');
    }

    private function registerEntry(string $tenantId, string $productId, int $quantity): void
    {
        $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => fake()->uuid(),
        ])->postJson('/api/v1/inventory/entries', [
            'product_id' => $productId,
            'type' => 'purchase',
            'quantity' => $quantity,
            'reason' => 'Compra de reposicao',
        ])->assertCreated();
    }

    private function registerOutput(string $tenantId, string $productId, int $quantity): void
    {
        $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => fake()->uuid(),
        ])->postJson('/api/v1/inventory/outputs', [
            'product_id' => $productId,
            'type' => 'service_consumption',
            'quantity' => $quantity,
            'reason' => 'Consumo em servico',
        ])->assertCreated();
    }
}
