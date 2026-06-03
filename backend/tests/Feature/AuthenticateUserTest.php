<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_authenticates_active_user_and_issues_token(): void
    {
        $tenantId = $this->createTenant();
        $userId = $this->createUser($tenantId);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@oficina.com',
            'password' => 'secret',
            'token_name' => 'mobile',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.id', (string) $userId)
            ->assertJsonPath('data.user.email', 'admin@oficina.com')
            ->assertJsonPath('data.user.role', 'admin')
            ->assertJsonPath('data.tenant.id', $tenantId)
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                ],
            ]);

        $plainToken = (string) $response->json('data.access_token');
        $this->assertNotSame('', $plainToken);

        $this->assertDatabaseHas('user_access_tokens', [
            'user_id' => $userId,
            'name' => 'mobile',
            'token_hash' => hash('sha256', $plainToken),
        ]);
    }

    public function test_it_issues_token_using_internal_user_id_when_public_id_exists(): void
    {
        $tenantId = $this->createTenant();
        $publicId = fake()->uuid();
        $userId = $this->createUser($tenantId, publicId: $publicId);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@oficina.com',
            'password' => 'secret',
            'token_name' => 'mobile',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.user.id', $publicId);

        $plainToken = (string) $response->json('data.access_token');

        $this->assertDatabaseHas('user_access_tokens', [
            'user_id' => $userId,
            'name' => 'mobile',
            'token_hash' => hash('sha256', $plainToken),
        ]);
    }

    public function test_it_rejects_invalid_credentials(): void
    {
        $tenantId = $this->createTenant();
        $this->createUser($tenantId);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@oficina.com',
            'password' => 'wrong',
        ])->assertUnauthorized()
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function test_it_rejects_inactive_user(): void
    {
        $tenantId = $this->createTenant();
        $this->createUser($tenantId, status: 'inactive');

        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@oficina.com',
            'password' => 'secret',
        ])->assertForbidden()
            ->assertJson([
                'message' => 'User account is inactive.',
            ]);
    }

    public function test_it_rejects_user_without_tenant(): void
    {
        $this->createUser(tenantId: null);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@oficina.com',
            'password' => 'secret',
        ])->assertForbidden()
            ->assertJson([
                'message' => 'User is not linked to a tenant.',
            ]);
    }

    public function test_it_rejects_invalid_payload(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'invalid',
            'password' => '',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
                'password',
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

    private function createUser(?string $tenantId, string $status = 'active', ?string $publicId = null): int
    {
        return (int) DB::table('users')->insertGetId([
            'tenant_id' => $tenantId,
            'public_id' => $publicId,
            'name' => 'Admin Oficina',
            'email' => 'admin@oficina.com',
            'password' => Hash::make('secret'),
            'status' => $status,
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
