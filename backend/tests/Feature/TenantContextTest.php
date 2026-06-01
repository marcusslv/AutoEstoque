<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_resolves_tenant_from_header(): void
    {
        $tenantId = '018f95f2-0f08-7f85-9b31-2d833a1a2f41';

        $response = $this->withHeader('X-Tenant-Id', $tenantId)
            ->getJson('/api/v1/context/tenant');

        $response->assertOk()
            ->assertJson([
                'tenant_id' => $tenantId,
            ]);
    }

    public function test_it_requires_tenant_header(): void
    {
        $response = $this->getJson('/api/v1/context/tenant');

        $response->assertUnprocessable()
            ->assertJson([
                'message' => 'The X-Tenant-Id header is required.',
            ]);
    }

    public function test_it_requires_valid_tenant_uuid(): void
    {
        $response = $this->withHeader('X-Tenant-Id', 'invalid')
            ->getJson('/api/v1/context/tenant');

        $response->assertUnprocessable()
            ->assertJson([
                'message' => 'The X-Tenant-Id header must be a valid UUID.',
            ]);
    }
}
