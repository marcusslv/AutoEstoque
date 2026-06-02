<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DevelopmentSeeder extends Seeder
{
    private const TENANT_ID = '018f95f2-0f08-7f85-9b31-2d833a1a2000';

    /**
     * @var array<string, string>
     */
    private const USERS = [
        'owner' => '018f95f2-0f08-7f85-9b31-2d833a1a2101',
        'manager' => '018f95f2-0f08-7f85-9b31-2d833a1a2102',
        'admin' => '018f95f2-0f08-7f85-9b31-2d833a1a2103',
        'mechanic' => '018f95f2-0f08-7f85-9b31-2d833a1a2104',
    ];

    /**
     * Seed demo data for local development.
     */
    public function run(): void
    {
        $now = now();

        DB::table('tenants')->upsert([
            [
                'id' => self::TENANT_ID,
                'name' => 'AutoEstoque Oficina Demo',
                'document' => '12345678000190',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['id'], ['name', 'document', 'status', 'updated_at']);

        $this->seedUsers($now);
        $this->seedProducts($now);
        $this->seedInventory($now);
        $this->seedVehicles($now);
        $this->seedServiceOrders($now);
    }

    private function seedUsers(mixed $now): void
    {
        $password = Hash::make('password');

        DB::table('users')->upsert([
            $this->userRow(self::USERS['owner'], 'Proprietario Demo', 'owner@autoestoque.test', 'owner', $password, $now),
            $this->userRow(self::USERS['manager'], 'Gerente Demo', 'manager@autoestoque.test', 'manager', $password, $now),
            $this->userRow(self::USERS['admin'], 'Admin Demo', 'admin@autoestoque.test', 'admin', $password, $now),
            $this->userRow(self::USERS['mechanic'], 'Mecanico Demo', 'mechanic@autoestoque.test', 'mechanic', $password, $now),
        ], ['email'], ['tenant_id', 'public_id', 'name', 'password', 'status', 'role', 'updated_at']);
    }

    /**
     * @return array<string, mixed>
     */
    private function userRow(
        string $publicId,
        string $name,
        string $email,
        string $role,
        string $password,
        mixed $now,
    ): array {
        return [
            'tenant_id' => self::TENANT_ID,
            'public_id' => $publicId,
            'name' => $name,
            'email' => $email,
            'email_verified_at' => $now,
            'password' => $password,
            'status' => 'active',
            'role' => $role,
            'remember_token' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function seedProducts(mixed $now): void
    {
        DB::table('products')->upsert([
            $this->productRow('018f95f2-0f08-7f85-9b31-2d833a1a2201', 'Filtro de oleo Tecfil PSL560', 'FO-TEC-560', '7891000000011', 'Filtros', 'Tecfil', 6, 2890, $now),
            $this->productRow('018f95f2-0f08-7f85-9b31-2d833a1a2202', 'Pastilha de freio dianteira Corolla', 'PF-COR-DIA', '7891000000028', 'Freios', 'Cobreq', 4, 15990, $now),
            $this->productRow('018f95f2-0f08-7f85-9b31-2d833a1a2203', 'Oleo 5W30 sintetico 1L', 'OL-5W30-1L', '7891000000035', 'Lubrificantes', 'Mobil', 12, 4290, $now),
            $this->productRow('018f95f2-0f08-7f85-9b31-2d833a1a2204', 'Vela de ignicao NGK BKR6E', 'VL-NGK-BKR6E', '7891000000042', 'Ignicao', 'NGK', 8, 2490, $now),
            $this->productRow('018f95f2-0f08-7f85-9b31-2d833a1a2205', 'Bateria Moura 60Ah', 'BT-MOU-60AH', '7891000000059', 'Eletrica', 'Moura', 2, 38990, $now),
        ], ['tenant_id', 'sku'], [
            'name',
            'barcode',
            'category',
            'brand',
            'supplier',
            'minimum_stock',
            'cost_in_cents',
            'currency',
            'updated_at',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function productRow(
        string $id,
        string $name,
        string $sku,
        string $barcode,
        string $category,
        string $brand,
        int $minimumStock,
        int $costInCents,
        mixed $now,
    ): array {
        return [
            'id' => $id,
            'tenant_id' => self::TENANT_ID,
            'name' => $name,
            'sku' => $sku,
            'barcode' => $barcode,
            'category' => $category,
            'brand' => $brand,
            'supplier' => 'Distribuidora Auto Pecas Central',
            'minimum_stock' => $minimumStock,
            'cost_in_cents' => $costInCents,
            'currency' => 'BRL',
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function seedInventory(mixed $now): void
    {
        DB::table('inventory_items')->upsert([
            $this->inventoryRow('018f95f2-0f08-7f85-9b31-2d833a1a2301', '018f95f2-0f08-7f85-9b31-2d833a1a2201', 10, $now),
            $this->inventoryRow('018f95f2-0f08-7f85-9b31-2d833a1a2302', '018f95f2-0f08-7f85-9b31-2d833a1a2202', 3, $now),
            $this->inventoryRow('018f95f2-0f08-7f85-9b31-2d833a1a2303', '018f95f2-0f08-7f85-9b31-2d833a1a2203', 18, $now),
            $this->inventoryRow('018f95f2-0f08-7f85-9b31-2d833a1a2304', '018f95f2-0f08-7f85-9b31-2d833a1a2204', 0, $now),
            $this->inventoryRow('018f95f2-0f08-7f85-9b31-2d833a1a2305', '018f95f2-0f08-7f85-9b31-2d833a1a2205', 1, $now),
        ], ['tenant_id', 'product_id'], ['current_stock', 'updated_at']);

        DB::table('stock_movements')->upsert([
            $this->movementRow('018f95f2-0f08-7f85-9b31-2d833a1a2401', '018f95f2-0f08-7f85-9b31-2d833a1a2201', self::USERS['admin'], 'entry', 'purchase', 12, 'Compra inicial para estoque demo', 2890, $now->copy()->subDays(5), $now),
            $this->movementRow('018f95f2-0f08-7f85-9b31-2d833a1a2402', '018f95f2-0f08-7f85-9b31-2d833a1a2202', self::USERS['admin'], 'entry', 'purchase', 5, 'Compra inicial para estoque demo', 15990, $now->copy()->subDays(4), $now),
            $this->movementRow('018f95f2-0f08-7f85-9b31-2d833a1a2403', '018f95f2-0f08-7f85-9b31-2d833a1a2203', self::USERS['admin'], 'entry', 'purchase', 20, 'Compra inicial para estoque demo', 4290, $now->copy()->subDays(3), $now),
            $this->movementRow('018f95f2-0f08-7f85-9b31-2d833a1a2404', '018f95f2-0f08-7f85-9b31-2d833a1a2203', self::USERS['mechanic'], 'output', 'service_consumption', 2, 'Consumo em ordem de servico', null, $now->copy()->subDay(), $now, 'Ordem de servico 018f95f2-0f08-7f85-9b31-2d833a1a2602'),
        ], ['id'], [
            'tenant_id',
            'product_id',
            'user_id',
            'direction',
            'type',
            'quantity',
            'reason',
            'note',
            'unit_cost_in_cents',
            'occurred_at',
            'updated_at',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function inventoryRow(string $id, string $productId, int $currentStock, mixed $now): array
    {
        return [
            'id' => $id,
            'tenant_id' => self::TENANT_ID,
            'product_id' => $productId,
            'current_stock' => $currentStock,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function movementRow(
        string $id,
        string $productId,
        string $userId,
        string $direction,
        string $type,
        int $quantity,
        string $reason,
        ?int $unitCostInCents,
        mixed $occurredAt,
        mixed $now,
        ?string $note = null,
    ): array {
        return [
            'id' => $id,
            'tenant_id' => self::TENANT_ID,
            'product_id' => $productId,
            'user_id' => $userId,
            'direction' => $direction,
            'type' => $type,
            'quantity' => $quantity,
            'reason' => $reason,
            'note' => $note,
            'unit_cost_in_cents' => $unitCostInCents,
            'occurred_at' => $occurredAt,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function seedVehicles(mixed $now): void
    {
        DB::table('vehicles')->upsert([
            $this->vehicleRow('018f95f2-0f08-7f85-9b31-2d833a1a2501', 'ABC1D23', 'Chevrolet', 'Onix', 2020, 'Joao Silva', '11999990000', $now),
            $this->vehicleRow('018f95f2-0f08-7f85-9b31-2d833a1a2502', 'XYZ9A88', 'Toyota', 'Corolla', 2019, 'Maria Souza', '11988887777', $now),
        ], ['tenant_id', 'plate'], [
            'brand',
            'model',
            'year',
            'owner_name',
            'owner_phone',
            'updated_at',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function vehicleRow(
        string $id,
        string $plate,
        string $brand,
        string $model,
        int $year,
        string $ownerName,
        string $ownerPhone,
        mixed $now,
    ): array {
        return [
            'id' => $id,
            'tenant_id' => self::TENANT_ID,
            'plate' => $plate,
            'brand' => $brand,
            'model' => $model,
            'year' => $year,
            'owner_name' => $ownerName,
            'owner_phone' => $ownerPhone,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function seedServiceOrders(mixed $now): void
    {
        DB::table('service_orders')->upsert([
            $this->serviceOrderRow(
                '018f95f2-0f08-7f85-9b31-2d833a1a2601',
                '018f95f2-0f08-7f85-9b31-2d833a1a2501',
                self::USERS['mechanic'],
                'Joao Silva',
                'Troca de oleo e filtro',
                'Cliente aguarda na recepcao',
                'open',
                $now->copy()->subHours(2),
                null,
                $now,
            ),
            $this->serviceOrderRow(
                '018f95f2-0f08-7f85-9b31-2d833a1a2602',
                '018f95f2-0f08-7f85-9b31-2d833a1a2502',
                self::USERS['mechanic'],
                'Maria Souza',
                'Revisao preventiva de 40 mil km',
                'Servico concluido com baixa automatica demo',
                'finished',
                $now->copy()->subDays(2),
                $now->copy()->subDay(),
                $now,
            ),
        ], ['id'], [
            'tenant_id',
            'vehicle_id',
            'created_by_user_id',
            'customer_name',
            'services_description',
            'observations',
            'status',
            'opened_at',
            'finished_at',
            'updated_at',
        ]);

        DB::table('service_order_items')->upsert([
            $this->serviceOrderItemRow('018f95f2-0f08-7f85-9b31-2d833a1a2701', '018f95f2-0f08-7f85-9b31-2d833a1a2601', '018f95f2-0f08-7f85-9b31-2d833a1a2201', self::USERS['mechanic'], 1, $now),
            $this->serviceOrderItemRow('018f95f2-0f08-7f85-9b31-2d833a1a2702', '018f95f2-0f08-7f85-9b31-2d833a1a2601', '018f95f2-0f08-7f85-9b31-2d833a1a2203', self::USERS['mechanic'], 4, $now),
            $this->serviceOrderItemRow('018f95f2-0f08-7f85-9b31-2d833a1a2703', '018f95f2-0f08-7f85-9b31-2d833a1a2602', '018f95f2-0f08-7f85-9b31-2d833a1a2203', self::USERS['mechanic'], 2, $now),
        ], ['id'], [
            'tenant_id',
            'service_order_id',
            'product_id',
            'added_by_user_id',
            'quantity',
            'updated_at',
        ]);

        DB::table('service_order_stock_movements')->upsert([
            [
                'id' => '018f95f2-0f08-7f85-9b31-2d833a1a2801',
                'tenant_id' => self::TENANT_ID,
                'service_order_id' => '018f95f2-0f08-7f85-9b31-2d833a1a2602',
                'service_order_item_id' => '018f95f2-0f08-7f85-9b31-2d833a1a2703',
                'stock_movement_id' => '018f95f2-0f08-7f85-9b31-2d833a1a2404',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['stock_movement_id'], [
            'tenant_id',
            'service_order_id',
            'service_order_item_id',
            'updated_at',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function serviceOrderRow(
        string $id,
        string $vehicleId,
        string $createdByUserId,
        string $customerName,
        string $servicesDescription,
        ?string $observations,
        string $status,
        mixed $openedAt,
        mixed $finishedAt,
        mixed $now,
    ): array {
        return [
            'id' => $id,
            'tenant_id' => self::TENANT_ID,
            'vehicle_id' => $vehicleId,
            'created_by_user_id' => $createdByUserId,
            'customer_name' => $customerName,
            'services_description' => $servicesDescription,
            'observations' => $observations,
            'status' => $status,
            'opened_at' => $openedAt,
            'finished_at' => $finishedAt,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serviceOrderItemRow(
        string $id,
        string $serviceOrderId,
        string $productId,
        string $addedByUserId,
        int $quantity,
        mixed $now,
    ): array {
        return [
            'id' => $id,
            'tenant_id' => self::TENANT_ID,
            'service_order_id' => $serviceOrderId,
            'product_id' => $productId,
            'added_by_user_id' => $addedByUserId,
            'quantity' => $quantity,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
