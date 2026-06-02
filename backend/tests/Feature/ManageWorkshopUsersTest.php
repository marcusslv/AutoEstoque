<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ManageWorkshopUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_and_lists_workshop_users_for_current_tenant(): void
    {
        $tenantId = $this->createTenant('Oficina A');
        $otherTenantId = $this->createTenant('Oficina B');
        $this->createUser($otherTenantId, email: 'other@oficina.com');

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->postJson('/api/v1/users', [
                'name' => 'Mecanico Oficina',
                'email' => 'mecanico@oficina.com',
                'password' => 'secret123',
                'role' => 'mechanic',
                'status' => 'active',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.tenant_id', $tenantId)
            ->assertJsonPath('data.name', 'Mecanico Oficina')
            ->assertJsonPath('data.email', 'mecanico@oficina.com')
            ->assertJsonPath('data.role', 'mechanic')
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('users', [
            'tenant_id' => $tenantId,
            'email' => 'mecanico@oficina.com',
            'role' => 'mechanic',
            'status' => 'active',
        ]);

        $this->withHeader('X-Tenant-Id', $tenantId)
            ->getJson('/api/v1/users')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.email', 'mecanico@oficina.com');
    }

    public function test_it_updates_and_deactivates_workshop_user(): void
    {
        $tenantId = $this->createTenant();
        $userId = $this->createUser($tenantId, email: 'admin@oficina.com');

        $this->withHeader('X-Tenant-Id', $tenantId)
            ->patchJson('/api/v1/users/'.$userId, [
                'name' => 'Gerente Oficina',
                'role' => 'manager',
                'status' => 'active',
            ])->assertOk()
            ->assertJsonPath('data.name', 'Gerente Oficina')
            ->assertJsonPath('data.role', 'manager');

        $this->withHeader('X-Tenant-Id', $tenantId)
            ->patchJson('/api/v1/users/'.$userId.'/deactivate')
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive');

        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'status' => 'inactive',
        ]);
    }

    public function test_it_rejects_duplicated_email(): void
    {
        $tenantId = $this->createTenant();
        $this->createUser($tenantId, email: 'admin@oficina.com');

        $this->withHeader('X-Tenant-Id', $tenantId)
            ->postJson('/api/v1/users', [
                'name' => 'Admin Duplicado',
                'email' => 'admin@oficina.com',
                'password' => 'secret123',
                'role' => 'admin',
            ])->assertConflict()
            ->assertJson([
                'message' => 'User email already exists.',
            ]);
    }

    public function test_it_rejects_user_limit_for_starter_plan(): void
    {
        $tenantId = $this->createTenant();
        $this->createUser($tenantId, email: 'one@oficina.com');
        $this->createUser($tenantId, email: 'two@oficina.com');
        $this->createUser($tenantId, email: 'three@oficina.com');

        $this->withHeader('X-Tenant-Id', $tenantId)
            ->postJson('/api/v1/users', [
                'name' => 'Quarto Usuario',
                'email' => 'four@oficina.com',
                'password' => 'secret123',
                'role' => 'mechanic',
            ])->assertConflict()
            ->assertJson([
                'message' => 'User limit reached for this tenant.',
            ]);
    }

    public function test_it_rejects_user_from_other_tenant(): void
    {
        $tenantId = $this->createTenant('Oficina A');
        $otherTenantId = $this->createTenant('Oficina B');
        $otherUserId = $this->createUser($otherTenantId, email: 'other@oficina.com');

        $this->withHeader('X-Tenant-Id', $tenantId)
            ->patchJson('/api/v1/users/'.$otherUserId, [
                'name' => 'Outro',
                'role' => 'admin',
                'status' => 'active',
            ])->assertNotFound()
            ->assertJson([
                'message' => 'User not found for this tenant.',
            ]);
    }

    public function test_it_rejects_invalid_payload(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeader('X-Tenant-Id', $tenantId)
            ->postJson('/api/v1/users', [
                'name' => '',
                'email' => 'invalid',
                'password' => 'short',
                'role' => 'invalid',
                'status' => 'invalid',
            ])->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
                'role',
                'status',
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

    private function createUser(string $tenantId, string $email): int
    {
        return (int) DB::table('users')->insertGetId([
            'tenant_id' => $tenantId,
            'name' => 'Usuario Oficina',
            'email' => $email,
            'password' => Hash::make('secret123'),
            'status' => 'active',
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
