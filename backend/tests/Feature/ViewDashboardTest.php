<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ViewDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_dashboard_indicators_for_current_tenant(): void
    {
        $tenantId = $this->createTenant('Oficina A');
        $otherTenantId = $this->createTenant('Oficina B');
        $lowProductId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001', minimumStock: 5, costInCents: 1000);
        $zeroProductId = $this->createProduct($tenantId, name: 'Pastilha de freio', sku: 'PF-001', barcode: '7891234567891', minimumStock: 3, costInCents: 2000);
        $availableProductId = $this->createProduct($tenantId, name: 'Correia dentada', sku: 'CD-001', barcode: '7891234567892', minimumStock: 2, costInCents: 3000);
        $neverMovedProductId = $this->createProduct($tenantId, name: 'Vela', sku: 'VL-001', barcode: '7891234567893', minimumStock: 1, costInCents: 4000);
        $otherProductId = $this->createProduct($otherTenantId, name: 'Bateria', sku: 'BT-001', barcode: '7891234567894', minimumStock: 5, costInCents: 9000);
        $userId = fake()->uuid();

        $this->registerEntry($tenantId, $userId, $lowProductId, 2);
        $this->registerEntry($tenantId, $userId, $zeroProductId, 1);
        $this->registerOutput($tenantId, $userId, $zeroProductId, 1);
        $this->registerEntry($tenantId, $userId, $availableProductId, 3);
        $this->registerEntry($otherTenantId, fake()->uuid(), $otherProductId, 1);

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/dashboard?recent_movements_limit=2');

        $response->assertOk()
            ->assertJsonPath('data.tenant_id', $tenantId)
            ->assertJsonPath('data.total_products', 4)
            ->assertJsonPath('data.products_below_minimum', 1)
            ->assertJsonPath('data.products_zero_stock', 2)
            ->assertJsonPath('data.total_stock_value_in_cents', 11000)
            ->assertJsonPath('data.today_movements', 4)
            ->assertJsonCount(2, 'data.recent_movements')
            ->assertJsonPath('data.recent_movements.0.product_id', $availableProductId);

        $this->assertNotSame($neverMovedProductId, $response->json('data.recent_movements.0.product_id'));
    }

    public function test_it_displays_empty_dashboard_when_tenant_has_no_data(): void
    {
        $tenantId = $this->createTenant();

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/dashboard');

        $response->assertOk()
            ->assertJsonPath('data.total_products', 0)
            ->assertJsonPath('data.products_below_minimum', 0)
            ->assertJsonPath('data.products_zero_stock', 0)
            ->assertJsonPath('data.total_stock_value_in_cents', 0)
            ->assertJsonPath('data.today_movements', 0)
            ->assertJsonPath('data.recent_movements', []);
    }

    public function test_it_rejects_invalid_filters(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/dashboard?date=invalid&recent_movements_limit=21')
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'date',
                'recent_movements_limit',
            ]);
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
        int $costInCents = 2590,
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
                'cost_in_cents' => $costInCents,
            ]);

        $response->assertCreated();

        return (string) $response->json('data.id');
    }
}
