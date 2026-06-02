<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GenerateMinimumStockAlertsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_products_below_or_equal_minimum_stock_for_current_tenant(): void
    {
        $tenantId = $this->createTenant('Oficina A');
        $otherTenantId = $this->createTenant('Oficina B');
        $lowProductId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001', minimumStock: 5);
        $regularProductId = $this->createProduct($tenantId, name: 'Pastilha de freio', sku: 'PF-001', barcode: '7891234567891', minimumStock: 2);
        $zeroProductId = $this->createProduct($tenantId, name: 'Correia dentada', sku: 'CD-001', barcode: '7891234567892', minimumStock: 3);
        $otherProductId = $this->createProduct($otherTenantId, name: 'Vela', sku: 'VL-001', barcode: '7891234567893', minimumStock: 5);
        $userId = fake()->uuid();

        $this->registerEntry($tenantId, $userId, $lowProductId, 2);
        $this->registerEntry($tenantId, $userId, $regularProductId, 4);
        $this->registerEntry($tenantId, $userId, $zeroProductId, 1);
        $this->registerOutput($tenantId, $userId, $zeroProductId, 1);
        $this->registerEntry($otherTenantId, fake()->uuid(), $otherProductId, 1);

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/inventory/alerts/minimum-stock');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.type', 'minimum_stock')
            ->assertJsonPath('data.0.product_id', $lowProductId)
            ->assertJsonPath('data.0.product.name', 'Filtro de oleo')
            ->assertJsonPath('data.0.product.sku', 'FO-001')
            ->assertJsonPath('data.0.current_stock', 2)
            ->assertJsonPath('data.0.minimum_stock', 5)
            ->assertJsonPath('data.0.shortage_quantity', 3);
    }

    public function test_it_returns_empty_state_when_stock_is_regular(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001', minimumStock: 2);

        $this->registerEntry($tenantId, fake()->uuid(), $productId, 3);

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/inventory/alerts/minimum-stock');

        $response->assertOk()
            ->assertJsonPath('meta.total', 0)
            ->assertJsonPath('data', []);
    }

    public function test_it_rejects_invalid_limit(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/inventory/alerts/minimum-stock?limit=101')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['limit']);
    }

    private function registerEntry(string $tenantId, string $userId, string $productId, int $quantity): void
    {
        $this->withHeaders($this->authHeaders($tenantId, $userId))->postJson('/api/v1/inventory/entries', [
            'product_id' => $productId,
            'type' => 'purchase',
            'quantity' => $quantity,
            'reason' => 'Compra de reposicao',
        ])->assertCreated();
    }

    private function registerOutput(string $tenantId, string $userId, string $productId, int $quantity): void
    {
        $this->withHeaders($this->authHeaders($tenantId, $userId))->postJson('/api/v1/inventory/outputs', [
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
        int $minimumStock = 2,
    ): string {
        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->postJson('/api/v1/products', [
                'name' => $name,
                'sku' => $sku,
                'barcode' => $barcode,
                'category' => 'Filtros',
                'brand' => 'Mann',
                'supplier' => 'Auto Pecas Central',
                'minimum_stock' => $minimumStock,
                'cost_in_cents' => 2590,
            ]);

        $response->assertCreated();

        return (string) $response->json('data.id');
    }
}
