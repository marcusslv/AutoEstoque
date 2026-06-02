<?php

namespace Tests\Feature;

use Database\Seeders\DevelopmentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DevelopmentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_development_dataset(): void
    {
        $this->seed(DevelopmentSeeder::class);

        $this->assertDatabaseHas('tenants', [
            'id' => '018f95f2-0f08-7f85-9b31-2d833a1a2000',
            'name' => 'AutoEstoque Oficina Demo',
        ]);

        $this->assertSame(4, DB::table('users')->count());
        $this->assertSame(5, DB::table('products')->count());
        $this->assertSame(5, DB::table('inventory_items')->count());
        $this->assertSame(2, DB::table('vehicles')->count());
        $this->assertSame(2, DB::table('service_orders')->count());
        $this->assertSame(3, DB::table('service_order_items')->count());
        $this->assertSame(1, DB::table('service_order_stock_movements')->count());

        $admin = DB::table('users')->where('email', 'admin@autoestoque.test')->first();

        $this->assertNotNull($admin);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function test_it_can_be_seeded_more_than_once_without_duplicates(): void
    {
        $this->seed(DevelopmentSeeder::class);
        $this->seed(DevelopmentSeeder::class);

        $this->assertSame(4, DB::table('users')->count());
        $this->assertSame(5, DB::table('products')->count());
        $this->assertSame(2, DB::table('service_orders')->count());
        $this->assertSame(1, DB::table('service_order_stock_movements')->count());
    }
}
