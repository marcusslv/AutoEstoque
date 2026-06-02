<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_mechanic_cannot_access_backoffice_user_management(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId, role: 'mechanic'))
            ->getJson('/api/v1/users')
            ->assertForbidden()
            ->assertJson([
                'message' => 'This action is not allowed for your profile.',
            ]);
    }

    public function test_mechanic_cannot_create_products_or_manual_inventory_movements(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId, role: 'mechanic'))
            ->postJson('/api/v1/products', [
                'name' => 'Filtro de oleo',
                'sku' => 'FO-001',
                'barcode' => null,
                'category' => 'Filtros',
                'brand' => 'Mann',
                'supplier' => 'Auto Pecas Central',
                'minimum_stock' => 2,
                'cost_in_cents' => 2590,
            ])
            ->assertForbidden();

        $this->withHeaders($this->authHeaders($tenantId, role: 'mechanic'))
            ->postJson('/api/v1/inventory/entries', [
                'product_id' => fake()->uuid(),
                'type' => 'purchase',
                'quantity' => 1,
                'reason' => 'Compra de reposicao',
            ])
            ->assertForbidden();
    }

    public function test_mechanic_can_access_workshop_operation_queries(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId, role: 'mechanic'))
            ->getJson('/api/v1/stock')
            ->assertOk();

        $this->withHeaders($this->authHeaders($tenantId, role: 'mechanic'))
            ->getJson('/api/v1/service-orders')
            ->assertOk();
    }

    public function test_owner_can_access_backoffice_user_management(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId, role: 'owner'))
            ->getJson('/api/v1/users')
            ->assertOk();
    }

    private function createTenant(): string
    {
        $tenantId = fake()->uuid();

        DB::table('tenants')->insert([
            'id' => $tenantId,
            'name' => 'Oficina Teste',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $tenantId;
    }
}
