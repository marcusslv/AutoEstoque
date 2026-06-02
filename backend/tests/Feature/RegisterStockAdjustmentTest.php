<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RegisterStockAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_registers_entry_adjustment_creating_inventory_item_and_movement(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId);
        $userId = fake()->uuid();

        $response = $this->withHeaders($this->authHeaders($tenantId, $userId))->postJson('/api/v1/inventory/adjustments', [
            'product_id' => $productId,
            'direction' => 'entry',
            'quantity' => 4,
            'reason' => 'Conferencia de estoque',
            'note' => 'Inventario semanal',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.tenant_id', $tenantId)
            ->assertJsonPath('data.product_id', $productId)
            ->assertJsonPath('data.direction', 'entry')
            ->assertJsonPath('data.type', 'manual_adjustment')
            ->assertJsonPath('data.quantity', 4)
            ->assertJsonPath('data.current_stock', 4)
            ->assertJsonPath('data.reason', 'Conferencia de estoque');

        $this->assertDatabaseHas('inventory_items', [
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'current_stock' => 4,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'user_id' => $userId,
            'direction' => 'entry',
            'type' => 'manual_adjustment',
            'quantity' => 4,
            'reason' => 'Conferencia de estoque',
            'unit_cost_in_cents' => null,
        ]);
    }

    public function test_it_registers_output_adjustment_decreasing_inventory_item(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId);
        $userId = fake()->uuid();

        $this->registerEntry($tenantId, $userId, $productId, 5);

        $response = $this->withHeaders($this->authHeaders($tenantId, $userId))->postJson('/api/v1/inventory/adjustments', [
            'product_id' => $productId,
            'direction' => 'output',
            'quantity' => 2,
            'reason' => 'Conferencia de estoque',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.direction', 'output')
            ->assertJsonPath('data.type', 'manual_adjustment')
            ->assertJsonPath('data.quantity', 2)
            ->assertJsonPath('data.current_stock', 3);

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
            'type' => 'manual_adjustment',
            'quantity' => 2,
            'reason' => 'Conferencia de estoque',
        ]);
    }

    public function test_it_rejects_output_adjustment_when_stock_is_insufficient(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId);
        $userId = fake()->uuid();

        $this->registerEntry($tenantId, $userId, $productId, 1);

        $this->withHeaders($this->authHeaders($tenantId, $userId))->postJson('/api/v1/inventory/adjustments', [
            'product_id' => $productId,
            'direction' => 'output',
            'quantity' => 2,
            'reason' => 'Conferencia de estoque',
        ])->assertStatus(409)
            ->assertJson([
                'message' => 'Insufficient stock for this operation.',
            ]);

        $this->assertDatabaseHas('inventory_items', [
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'current_stock' => 1,
        ]);
    }

    public function test_it_rejects_missing_product(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId, fake()->uuid()))->postJson('/api/v1/inventory/adjustments', [
            'product_id' => fake()->uuid(),
            'direction' => 'entry',
            'quantity' => 1,
            'reason' => 'Conferencia de estoque',
        ])->assertNotFound()
            ->assertJson([
                'message' => 'Product not found for this tenant.',
            ]);
    }

    public function test_it_rejects_invalid_payload(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId))
            ->postJson('/api/v1/inventory/adjustments', [
                'product_id' => 'invalid',
                'direction' => 'invalid',
                'quantity' => 0,
                'reason' => '',
            ])->assertUnprocessable()
            ->assertJsonValidationErrors([
                'product_id',
                'direction',
                'quantity',
                'reason',
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

    private function createProduct(string $tenantId): string
    {
        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->postJson('/api/v1/products', [
                'name' => 'Filtro de oleo',
                'sku' => 'FO-001',
                'barcode' => '7891234567890',
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
