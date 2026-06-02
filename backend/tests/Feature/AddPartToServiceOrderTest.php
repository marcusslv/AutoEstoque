<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AddPartToServiceOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_adds_a_part_to_a_service_order_without_decreasing_stock(): void
    {
        $tenantId = $this->createTenant();
        $serviceOrderId = $this->createServiceOrder($tenantId);
        $productId = $this->createProduct($tenantId);
        $this->createInventoryItem($tenantId, $productId, currentStock: 5);
        $userId = fake()->uuid();

        $response = $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => $userId,
        ])->postJson("/api/v1/service-orders/{$serviceOrderId}/parts", [
            'product_id' => $productId,
            'quantity' => 2,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.tenant_id', $tenantId)
            ->assertJsonPath('data.service_order_id', $serviceOrderId)
            ->assertJsonPath('data.product_id', $productId)
            ->assertJsonPath('data.added_by_user_id', $userId)
            ->assertJsonPath('data.quantity', 2);

        $this->assertDatabaseHas('service_order_items', [
            'tenant_id' => $tenantId,
            'service_order_id' => $serviceOrderId,
            'product_id' => $productId,
            'added_by_user_id' => $userId,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('inventory_items', [
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'current_stock' => 5,
        ]);
    }

    public function test_it_rejects_unknown_service_order(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId);
        $this->createInventoryItem($tenantId, $productId, currentStock: 5);

        $response = $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => fake()->uuid(),
        ])->postJson('/api/v1/service-orders/'.fake()->uuid().'/parts', [
            'product_id' => $productId,
            'quantity' => 1,
        ]);

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Service order not found.',
            ]);
    }

    public function test_it_rejects_finished_service_order(): void
    {
        $tenantId = $this->createTenant();
        $serviceOrderId = $this->createServiceOrder($tenantId, status: 'finished');
        $productId = $this->createProduct($tenantId);
        $this->createInventoryItem($tenantId, $productId, currentStock: 5);

        $response = $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => fake()->uuid(),
        ])->postJson("/api/v1/service-orders/{$serviceOrderId}/parts", [
            'product_id' => $productId,
            'quantity' => 1,
        ]);

        $response->assertConflict()
            ->assertJson([
                'message' => 'Service order is not open.',
            ]);
    }

    public function test_it_rejects_unknown_product(): void
    {
        $tenantId = $this->createTenant();
        $serviceOrderId = $this->createServiceOrder($tenantId);

        $response = $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => fake()->uuid(),
        ])->postJson("/api/v1/service-orders/{$serviceOrderId}/parts", [
            'product_id' => fake()->uuid(),
            'quantity' => 1,
        ]);

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Product not found for this tenant.',
            ]);
    }

    public function test_it_rejects_insufficient_stock(): void
    {
        $tenantId = $this->createTenant();
        $serviceOrderId = $this->createServiceOrder($tenantId);
        $productId = $this->createProduct($tenantId);
        $this->createInventoryItem($tenantId, $productId, currentStock: 1);

        $response = $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => fake()->uuid(),
        ])->postJson("/api/v1/service-orders/{$serviceOrderId}/parts", [
            'product_id' => $productId,
            'quantity' => 2,
        ]);

        $response->assertConflict()
            ->assertJson([
                'message' => 'Insufficient stock for this operation.',
            ]);
    }

    public function test_it_rejects_invalid_payload(): void
    {
        $tenantId = $this->createTenant();
        $serviceOrderId = $this->createServiceOrder($tenantId);

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->postJson("/api/v1/service-orders/{$serviceOrderId}/parts", [
                'product_id' => 'invalid',
                'quantity' => 0,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'X-User-Id',
                'product_id',
                'quantity',
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

    private function createServiceOrder(string $tenantId, string $status = 'open'): string
    {
        $serviceOrderId = fake()->uuid();

        DB::table('service_orders')->insert([
            'id' => $serviceOrderId,
            'tenant_id' => $tenantId,
            'vehicle_id' => $this->createVehicle($tenantId),
            'created_by_user_id' => fake()->uuid(),
            'customer_name' => 'Joao Silva',
            'services_description' => 'Troca de oleo',
            'observations' => null,
            'status' => $status,
            'opened_at' => now(),
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
            'sku' => strtoupper(fake()->bothify('SKU-####')),
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

    private function createInventoryItem(string $tenantId, string $productId, int $currentStock): void
    {
        DB::table('inventory_items')->insert([
            'id' => fake()->uuid(),
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'current_stock' => $currentStock,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
