<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LogoutUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_revokes_current_access_token(): void
    {
        $tenantId = $this->createTenant();
        $headers = $this->authHeaders($tenantId);
        $plainToken = str_replace('Bearer ', '', $headers['Authorization']);

        $this->assertDatabaseHas('user_access_tokens', [
            'token_hash' => hash('sha256', $plainToken),
        ]);

        $response = $this->withHeaders($headers)
            ->postJson('/api/v1/auth/logout');

        $response->assertOk()
            ->assertJson([
                'message' => 'Logged out successfully.',
            ]);

        $this->assertDatabaseMissing('user_access_tokens', [
            'token_hash' => hash('sha256', $plainToken),
        ]);

        $this->withHeaders($headers)
            ->getJson('/api/v1/context/tenant')
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Invalid bearer token.',
            ]);
    }

    public function test_it_requires_bearer_token(): void
    {
        $this->postJson('/api/v1/auth/logout')
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Bearer token is required.',
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
}
