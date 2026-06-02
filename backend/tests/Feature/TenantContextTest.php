<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_resolves_tenant_from_authenticated_user(): void
    {
        $tenantId = '018f95f2-0f08-7f85-9b31-2d833a1a2f41';

        $response = $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/context/tenant');

        $response->assertOk()
            ->assertJson([
                'tenant_id' => $tenantId,
            ]);
    }

    public function test_it_requires_bearer_token(): void
    {
        $response = $this->getJson('/api/v1/context/tenant');

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Bearer token is required.',
            ]);
    }

    public function test_it_rejects_invalid_bearer_token(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/api/v1/context/tenant');

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Invalid bearer token.',
            ]);
    }
}
