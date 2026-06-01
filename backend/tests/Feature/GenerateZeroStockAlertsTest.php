<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GenerateZeroStockAlertsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_products_with_zero_stock_for_current_tenant(): void
    {
        $tenantId = $this->createTenant('Oficina A');
        $otherTenantId = $this->createTenant('Oficina B');
        $neverMovedProductId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001', minimumStock: 2);
        $zeroedProductId = $this->createProduct($tenantId, name: 'Pastilha de freio', sku: 'PF-001', barcode: '7891234567891', minimumStock: 3);
        $availableProductId = $this->createProduct($tenantId, name: 'Correia dentada', sku: 'CD-001', barcode: '7891234567892', minimumStock: 3);
        $otherProductId = $this->createProduct($otherTenantId, name: 'Vela', sku: 'VL-001', barcode: '7891234567893', minimumStock: 1);
        $userId = fake()->uuid();

        $this->registerEntry($tenantId, $userId, $zeroedProductId, 1);
        $this->registerOutput($tenantId, $userId, $zeroedProductId, 1);
        $this->registerEntry($tenantId, $userId, $availableProductId, 1);
        $this->registerEntry($otherTenantId, fake()->uuid(), $otherProductId, 1);
        $this->registerOutput($otherTenantId, fake()->uuid(), $otherProductId, 1);

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->getJson('/api/v1/inventory/alerts/zero-stock');

        $response->assertOk()
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('data.0.type', 'zero_stock')
            ->assertJsonPath('data.0.product_id', $neverMovedProductId)
            ->assertJsonPath('data.0.current_stock', 0)
            ->assertJsonPath('data.1.product_id', $zeroedProductId)
            ->assertJsonPath('data.1.current_stock', 0);
    }

    public function test_it_returns_empty_state_when_no_product_is_zeroed(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001', minimumStock: 2);

        $this->registerEntry($tenantId, fake()->uuid(), $productId, 1);

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->getJson('/api/v1/inventory/alerts/zero-stock');

        $response->assertOk()
            ->assertJsonPath('meta.total', 0)
            ->assertJsonPath('data', []);
    }

    public function test_it_rejects_invalid_limit(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeader('X-Tenant-Id', $tenantId)
            ->getJson('/api/v1/inventory/alerts/zero-stock?limit=101')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['limit']);
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
        int $minimumStock = 2,
    ): string {
        $response = $this->withHeader('X-Tenant-Id', $tenantId)
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
