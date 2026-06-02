<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FinishServiceOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_finishes_service_order_with_automatic_stock_output(): void
    {
        $tenantId = $this->createTenant();
        $serviceOrderId = $this->createServiceOrder($tenantId);
        $productId = $this->createProduct($tenantId);
        $this->createInventoryItem($tenantId, $productId, currentStock: 5);
        $this->createServiceOrderItem($tenantId, $serviceOrderId, $productId, quantity: 2);
        $userId = fake()->uuid();

        $response = $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => $userId,
        ])->patchJson("/api/v1/service-orders/{$serviceOrderId}/finish");

        $response->assertOk()
            ->assertJsonPath('data.tenant_id', $tenantId)
            ->assertJsonPath('data.status', 'finished')
            ->assertJsonCount(1, 'data.movement_ids');

        $this->assertDatabaseHas('service_orders', [
            'id' => $serviceOrderId,
            'tenant_id' => $tenantId,
            'status' => 'finished',
        ]);

        $this->assertDatabaseHas('inventory_items', [
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'current_stock' => 3,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'user_id' => $userId,
            'direction' => 'output',
            'type' => 'service_consumption',
            'quantity' => 2,
            'reason' => 'Consumo em ordem de servico',
            'note' => "Ordem de servico {$serviceOrderId}",
        ]);
    }

    public function test_it_rejects_service_order_without_parts(): void
    {
        $tenantId = $this->createTenant();
        $serviceOrderId = $this->createServiceOrder($tenantId);

        $response = $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => fake()->uuid(),
        ])->patchJson("/api/v1/service-orders/{$serviceOrderId}/finish");

        $response->assertConflict()
            ->assertJson([
                'message' => 'Service order has no parts.',
            ]);
    }

    public function test_it_rejects_insufficient_stock_and_keeps_order_open(): void
    {
        $tenantId = $this->createTenant();
        $serviceOrderId = $this->createServiceOrder($tenantId);
        $productId = $this->createProduct($tenantId);
        $this->createInventoryItem($tenantId, $productId, currentStock: 1);
        $this->createServiceOrderItem($tenantId, $serviceOrderId, $productId, quantity: 2);

        $response = $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => fake()->uuid(),
        ])->patchJson("/api/v1/service-orders/{$serviceOrderId}/finish");

        $response->assertConflict()
            ->assertJson([
                'message' => 'Insufficient stock for this operation.',
            ]);

        $this->assertDatabaseHas('service_orders', [
            'id' => $serviceOrderId,
            'status' => 'open',
            'finished_at' => null,
        ]);

        $this->assertDatabaseHas('inventory_items', [
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'current_stock' => 1,
        ]);

        $this->assertDatabaseMissing('stock_movements', [
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'type' => 'service_consumption',
        ]);
    }

    public function test_it_rejects_already_finished_order_to_avoid_duplicate_outputs(): void
    {
        $tenantId = $this->createTenant();
        $serviceOrderId = $this->createServiceOrder($tenantId, status: 'finished', finishedAt: now());

        $response = $this->withHeaders([
            'X-Tenant-Id' => $tenantId,
            'X-User-Id' => fake()->uuid(),
        ])->patchJson("/api/v1/service-orders/{$serviceOrderId}/finish");

        $response->assertConflict()
            ->assertJson([
                'message' => 'Service order is not open.',
            ]);
    }

    public function test_it_rejects_invalid_payload(): void
    {
        $tenantId = $this->createTenant();
        $serviceOrderId = $this->createServiceOrder($tenantId);

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->patchJson("/api/v1/service-orders/{$serviceOrderId}/finish");

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'X-User-Id',
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

    private function createServiceOrder(string $tenantId, string $status = 'open', mixed $finishedAt = null): string
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
            'finished_at' => $finishedAt,
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

    private function createServiceOrderItem(string $tenantId, string $serviceOrderId, string $productId, int $quantity): void
    {
        DB::table('service_order_items')->insert([
            'id' => fake()->uuid(),
            'tenant_id' => $tenantId,
            'service_order_id' => $serviceOrderId,
            'product_id' => $productId,
            'added_by_user_id' => fake()->uuid(),
            'quantity' => $quantity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
