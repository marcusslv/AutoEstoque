<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ListStockMovementHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_movements_for_current_tenant(): void
    {
        $tenantId = $this->createTenant('Oficina A');
        $otherTenantId = $this->createTenant('Oficina B');
        $productId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001');
        $otherProductId = $this->createProduct($otherTenantId, name: 'Pastilha de freio', sku: 'PF-001');

        $this->registerEntry($tenantId, fake()->uuid(), $productId, 5);
        $this->registerEntry($otherTenantId, fake()->uuid(), $otherProductId, 3);

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/inventory/movements');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.product_id', $productId)
            ->assertJsonPath('data.0.product.name', 'Filtro de oleo')
            ->assertJsonPath('data.0.product.sku', 'FO-001')
            ->assertJsonPath('data.0.direction', 'entry')
            ->assertJsonPath('data.0.type', 'purchase')
            ->assertJsonPath('data.0.quantity', 5);
    }

    public function test_it_filters_movements_by_product_direction_type_and_user(): void
    {
        $tenantId = $this->createTenant();
        $firstProductId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001');
        $secondProductId = $this->createProduct($tenantId, name: 'Pastilha de freio', sku: 'PF-001', barcode: '7891234567891');
        $userId = fake()->uuid();

        $this->registerEntry($tenantId, $userId, $firstProductId, 5);
        $this->registerOutput($tenantId, $userId, $firstProductId, 2);
        $this->registerEntry($tenantId, fake()->uuid(), $secondProductId, 1);

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/inventory/movements?product_id='.$firstProductId.'&direction=output&type=service_consumption&user_id='.$userId);

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.product_id', $firstProductId)
            ->assertJsonPath('data.0.user_id', $userId)
            ->assertJsonPath('data.0.direction', 'output')
            ->assertJsonPath('data.0.type', 'service_consumption')
            ->assertJsonPath('data.0.quantity', 2);
    }

    public function test_it_filters_movements_by_date_range(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001');
        $userId = fake()->uuid();

        $this->registerEntry($tenantId, $userId, $productId, 5);

        $today = now()->toDateString();
        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/inventory/movements?occurred_from='.$today.'&occurred_to='.$today);

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.product_id', $productId);
    }

    public function test_it_includes_service_order_origin_when_movement_is_linked_to_service_order(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001');
        $this->registerEntry($tenantId, fake()->uuid(), $productId, 5);
        $movementId = $this->registerOutput($tenantId, fake()->uuid(), $productId, 2)->json('data.movement_id');
        $vehicleId = $this->createVehicle($tenantId);
        $serviceOrderId = $this->createServiceOrder($tenantId, $vehicleId);
        $serviceOrderItemId = $this->createServiceOrderItem($tenantId, $serviceOrderId, $productId, 2);
        $this->createServiceOrderStockMovementLink($tenantId, $serviceOrderId, $serviceOrderItemId, $movementId);

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/inventory/movements?type=service_consumption');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $movementId)
            ->assertJsonPath('data.0.service_order.id', $serviceOrderId)
            ->assertJsonPath('data.0.service_order.item_id', $serviceOrderItemId);
    }

    public function test_it_returns_null_service_order_when_movement_is_not_linked_to_service_order(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001');

        $this->registerEntry($tenantId, fake()->uuid(), $productId, 5);

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/inventory/movements');

        $response->assertOk()
            ->assertJsonPath('data.0.service_order', null);
    }

    public function test_it_returns_empty_state_when_no_movement_matches_filters(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId, name: 'Filtro de oleo', sku: 'FO-001');

        $this->registerEntry($tenantId, fake()->uuid(), $productId, 5);

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/inventory/movements?direction=output');

        $response->assertOk()
            ->assertJsonPath('meta.total', 0)
            ->assertJsonPath('data', []);
    }

    public function test_it_rejects_invalid_filters(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/inventory/movements?product_id=invalid&direction=invalid&user_id=invalid&occurred_from=2026-06-02&occurred_to=2026-06-01&limit=101')
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'product_id',
                'direction',
                'user_id',
                'occurred_to',
                'limit',
            ]);
    }

    private function registerEntry(string $tenantId, string $userId, string $productId, int $quantity): TestResponse
    {
        return $this->withHeaders($this->authHeaders($tenantId, $userId))->postJson('/api/v1/inventory/entries', [
            'product_id' => $productId,
            'type' => 'purchase',
            'quantity' => $quantity,
            'reason' => 'Compra de reposicao',
        ])->assertCreated();
    }

    private function registerOutput(string $tenantId, string $userId, string $productId, int $quantity): TestResponse
    {
        return $this->withHeaders($this->authHeaders($tenantId, $userId))->postJson('/api/v1/inventory/outputs', [
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
        $response = $this->withHeaders($this->authHeaders($tenantId))
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

    private function createServiceOrder(string $tenantId, string $vehicleId): string
    {
        $serviceOrderId = fake()->uuid();

        DB::table('service_orders')->insert([
            'id' => $serviceOrderId,
            'tenant_id' => $tenantId,
            'vehicle_id' => $vehicleId,
            'created_by_user_id' => fake()->uuid(),
            'customer_name' => 'Joao Silva',
            'services_description' => 'Troca de oleo',
            'observations' => null,
            'status' => 'finished',
            'opened_at' => now(),
            'finished_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $serviceOrderId;
    }

    private function createServiceOrderItem(string $tenantId, string $serviceOrderId, string $productId, int $quantity): string
    {
        $serviceOrderItemId = fake()->uuid();

        DB::table('service_order_items')->insert([
            'id' => $serviceOrderItemId,
            'tenant_id' => $tenantId,
            'service_order_id' => $serviceOrderId,
            'product_id' => $productId,
            'added_by_user_id' => fake()->uuid(),
            'quantity' => $quantity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $serviceOrderItemId;
    }

    private function createServiceOrderStockMovementLink(
        string $tenantId,
        string $serviceOrderId,
        string $serviceOrderItemId,
        string $stockMovementId,
    ): void {
        DB::table('service_order_stock_movements')->insert([
            'id' => fake()->uuid(),
            'tenant_id' => $tenantId,
            'service_order_id' => $serviceOrderId,
            'service_order_item_id' => $serviceOrderItemId,
            'stock_movement_id' => $stockMovementId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
