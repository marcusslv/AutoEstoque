<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ShowServiceOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_service_order_details_with_vehicle_and_parts(): void
    {
        $tenantId = $this->createTenant();
        $vehicleId = $this->createVehicle($tenantId);
        $serviceOrderId = $this->createServiceOrder($tenantId, $vehicleId);
        $productId = $this->createProduct($tenantId);
        $itemId = $this->createServiceOrderItem($tenantId, $serviceOrderId, $productId, quantity: 2);

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson("/api/v1/service-orders/{$serviceOrderId}");

        $response->assertOk()
            ->assertJsonPath('data.id', $serviceOrderId)
            ->assertJsonPath('data.tenant_id', $tenantId)
            ->assertJsonPath('data.customer_name', 'Joao Silva')
            ->assertJsonPath('data.status', 'open')
            ->assertJsonPath('data.vehicle.id', $vehicleId)
            ->assertJsonPath('data.vehicle.plate', 'ABC1D23')
            ->assertJsonPath('data.parts.0.id', $itemId)
            ->assertJsonPath('data.parts.0.product_id', $productId)
            ->assertJsonPath('data.parts.0.product_name', 'Filtro de oleo')
            ->assertJsonPath('data.parts.0.quantity', 2)
            ->assertJsonPath('data.parts.0.movements', [])
            ->assertJsonPath('data.parts.0.movements_total', 0)
            ->assertJsonPath('meta.parts_total', 1);
    }

    public function test_it_shows_stock_movements_linked_to_service_order_parts(): void
    {
        $tenantId = $this->createTenant();
        $vehicleId = $this->createVehicle($tenantId);
        $serviceOrderId = $this->createServiceOrder($tenantId, $vehicleId);
        $productId = $this->createProduct($tenantId);
        $itemId = $this->createServiceOrderItem($tenantId, $serviceOrderId, $productId, quantity: 2);
        $movementId = $this->createStockMovement($tenantId, $productId, quantity: 2);
        $this->createServiceOrderStockMovementLink($tenantId, $serviceOrderId, $itemId, $movementId);

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson("/api/v1/service-orders/{$serviceOrderId}");

        $response->assertOk()
            ->assertJsonPath('data.parts.0.id', $itemId)
            ->assertJsonPath('data.parts.0.movements_total', 1)
            ->assertJsonPath('data.parts.0.movements.0.id', $movementId)
            ->assertJsonPath('data.parts.0.movements.0.direction', 'output')
            ->assertJsonPath('data.parts.0.movements.0.type', 'service_consumption')
            ->assertJsonPath('data.parts.0.movements.0.quantity', 2)
            ->assertJsonPath('data.parts.0.movements.0.reason', 'Consumo em ordem de servico');
    }

    public function test_it_rejects_service_order_from_another_tenant(): void
    {
        $firstTenantId = $this->createTenant('Oficina A');
        $secondTenantId = $this->createTenant('Oficina B');
        $vehicleId = $this->createVehicle($firstTenantId);
        $serviceOrderId = $this->createServiceOrder($firstTenantId, $vehicleId);

        $this->withHeaders($this->authHeaders($secondTenantId))
            ->getJson("/api/v1/service-orders/{$serviceOrderId}")
            ->assertNotFound()
            ->assertJson([
                'message' => 'Service order not found.',
            ]);
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
            'plate' => 'ABC1D23',
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
            'observations' => 'Cliente aguardando',
            'status' => 'open',
            'opened_at' => now(),
            'finished_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $serviceOrderId;
    }

    private function createProduct(string $tenantId): string
    {
        $productId = fake()->uuid();

        DB::table('products')->insert([
            'id' => $productId,
            'tenant_id' => $tenantId,
            'name' => 'Filtro de oleo',
            'sku' => 'FO-001',
            'barcode' => null,
            'category' => 'Filtros',
            'brand' => 'Mann',
            'supplier' => 'Auto Pecas Central',
            'minimum_stock' => 1,
            'cost_in_cents' => 2590,
            'currency' => 'BRL',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $productId;
    }

    private function createServiceOrderItem(string $tenantId, string $serviceOrderId, string $productId, int $quantity): string
    {
        $itemId = fake()->uuid();

        DB::table('service_order_items')->insert([
            'id' => $itemId,
            'tenant_id' => $tenantId,
            'service_order_id' => $serviceOrderId,
            'product_id' => $productId,
            'added_by_user_id' => fake()->uuid(),
            'quantity' => $quantity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $itemId;
    }

    private function createStockMovement(string $tenantId, string $productId, int $quantity): string
    {
        $movementId = fake()->uuid();

        DB::table('stock_movements')->insert([
            'id' => $movementId,
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'user_id' => fake()->uuid(),
            'direction' => 'output',
            'type' => 'service_consumption',
            'quantity' => $quantity,
            'reason' => 'Consumo em ordem de servico',
            'note' => null,
            'unit_cost_in_cents' => null,
            'occurred_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $movementId;
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
