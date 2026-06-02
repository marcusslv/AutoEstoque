<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ListServiceOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_service_orders_for_current_tenant(): void
    {
        $tenantId = $this->createTenant('Oficina A');
        $otherTenantId = $this->createTenant('Oficina B');
        $openOrderId = $this->createServiceOrder(
            tenantId: $tenantId,
            vehicleId: $this->createVehicle($tenantId, plate: 'ABC1D23'),
            customerName: 'Joao Silva',
            status: 'open',
        );
        $finishedOrderId = $this->createServiceOrder(
            tenantId: $tenantId,
            vehicleId: $this->createVehicle($tenantId, plate: 'XYZ9A88'),
            customerName: 'Maria Souza',
            status: 'finished',
        );
        $productId = $this->createProduct($tenantId);
        $this->createServiceOrderItem($tenantId, $openOrderId, $productId);
        $this->createServiceOrder($otherTenantId, $this->createVehicle($otherTenantId), customerName: 'Outro Cliente');

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/service-orders');

        $response->assertOk()
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('data.0.id', $finishedOrderId)
            ->assertJsonPath('data.0.status', 'finished')
            ->assertJsonPath('data.1.id', $openOrderId)
            ->assertJsonPath('data.1.parts_total', 1);
    }

    public function test_it_filters_service_orders_by_status(): void
    {
        $tenantId = $this->createTenant();
        $this->createServiceOrder($tenantId, $this->createVehicle($tenantId, plate: 'ABC1D23'), customerName: 'Ordem Aberta', status: 'open');
        $this->createServiceOrder($tenantId, $this->createVehicle($tenantId, plate: 'XYZ9A88'), customerName: 'Ordem Finalizada', status: 'finished');

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/service-orders?status=open');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.customer_name', 'Ordem Aberta')
            ->assertJsonPath('data.0.status', 'open');
    }

    public function test_it_filters_service_orders_by_search_term(): void
    {
        $tenantId = $this->createTenant();
        $this->createServiceOrder(
            tenantId: $tenantId,
            vehicleId: $this->createVehicle($tenantId, plate: 'ABC1D23', ownerName: 'Joao Silva'),
            customerName: 'Cliente Alfa',
        );
        $this->createServiceOrder(
            tenantId: $tenantId,
            vehicleId: $this->createVehicle($tenantId, plate: 'XYZ9A88', ownerName: 'Maria Souza'),
            customerName: 'Cliente Beta',
        );

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/service-orders?search=xyz');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.customer_name', 'Cliente Beta')
            ->assertJsonPath('data.0.vehicle.plate', 'XYZ9A88');
    }

    public function test_it_rejects_invalid_filters(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/service-orders?status=invalid&limit=0')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status', 'limit']);
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

    private function createVehicle(
        string $tenantId,
        string $plate = 'ABC1D23',
        string $ownerName = 'Joao Silva',
    ): string {
        $vehicleId = fake()->uuid();

        DB::table('vehicles')->insert([
            'id' => $vehicleId,
            'tenant_id' => $tenantId,
            'plate' => $plate,
            'brand' => 'Chevrolet',
            'model' => 'Onix',
            'year' => 2020,
            'owner_name' => $ownerName,
            'owner_phone' => '11999990000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $vehicleId;
    }

    private function createServiceOrder(
        string $tenantId,
        string $vehicleId,
        string $customerName,
        string $status = 'open',
    ): string {
        $serviceOrderId = fake()->uuid();

        DB::table('service_orders')->insert([
            'id' => $serviceOrderId,
            'tenant_id' => $tenantId,
            'vehicle_id' => $vehicleId,
            'created_by_user_id' => fake()->uuid(),
            'customer_name' => $customerName,
            'services_description' => 'Troca de oleo',
            'observations' => null,
            'status' => $status,
            'opened_at' => $status === 'finished' ? now()->subHour() : now()->subHours(2),
            'finished_at' => $status === 'finished' ? now() : null,
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

    private function createServiceOrderItem(string $tenantId, string $serviceOrderId, string $productId): void
    {
        DB::table('service_order_items')->insert([
            'id' => fake()->uuid(),
            'tenant_id' => $tenantId,
            'service_order_id' => $serviceOrderId,
            'product_id' => $productId,
            'added_by_user_id' => fake()->uuid(),
            'quantity' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
