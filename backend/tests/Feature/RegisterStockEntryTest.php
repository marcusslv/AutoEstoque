<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RegisterStockEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_registers_stock_entry_creating_inventory_item_and_movement(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId);
        $userId = fake()->uuid();

        $response = $this->withHeaders($this->authHeaders($tenantId, $userId))->postJson('/api/v1/inventory/entries', [
            'product_id' => $productId,
            'type' => 'purchase',
            'quantity' => 5,
            'reason' => 'Compra de reposicao',
            'note' => 'Nota 123',
            'unit_cost_in_cents' => 2590,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.tenant_id', $tenantId)
            ->assertJsonPath('data.product_id', $productId)
            ->assertJsonPath('data.type', 'purchase')
            ->assertJsonPath('data.quantity', 5)
            ->assertJsonPath('data.current_stock', 5)
            ->assertJsonPath('data.reason', 'Compra de reposicao')
            ->assertJsonPath('data.unit_cost_in_cents', 2590);

        $this->assertDatabaseHas('inventory_items', [
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'current_stock' => 5,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'user_id' => $userId,
            'direction' => 'entry',
            'type' => 'purchase',
            'quantity' => 5,
            'reason' => 'Compra de reposicao',
            'unit_cost_in_cents' => 2590,
        ]);
    }

    public function test_it_increases_existing_inventory_item(): void
    {
        $tenantId = $this->createTenant();
        $productId = $this->createProduct($tenantId);
        $userId = fake()->uuid();

        $this->registerEntry($tenantId, $userId, $productId, 5);
        $response = $this->registerEntry($tenantId, $userId, $productId, 3);

        $response->assertCreated()
            ->assertJsonPath('data.current_stock', 8);

        $this->assertDatabaseHas('inventory_items', [
            'tenant_id' => $tenantId,
            'product_id' => $productId,
            'current_stock' => 8,
        ]);

        $this->assertSame(2, DB::table('stock_movements')->where('product_id', $productId)->count());
    }

    public function test_it_rejects_missing_product(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId, fake()->uuid()))->postJson('/api/v1/inventory/entries', [
            'product_id' => fake()->uuid(),
            'type' => 'purchase',
            'quantity' => 5,
            'reason' => 'Compra de reposicao',
        ])->assertNotFound()
            ->assertJson([
                'message' => 'Product not found for this tenant.',
            ]);
    }

    public function test_it_rejects_invalid_payload(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId))
            ->postJson('/api/v1/inventory/entries', [
                'product_id' => 'invalid',
                'type' => 'invalid',
                'quantity' => 0,
                'reason' => '',
            ])->assertUnprocessable()
            ->assertJsonValidationErrors([
                'product_id',
                'type',
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
