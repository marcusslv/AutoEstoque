<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ManageWorkshopSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_default_settings_for_current_tenant(): void
    {
        $tenantId = $this->createTenant('Oficina Config');

        $this->withHeaders($this->authHeaders($tenantId))
            ->getJson('/api/v1/settings/workshop')
            ->assertOk()
            ->assertJsonPath('data.tenant_id', $tenantId)
            ->assertJsonPath('data.display_name', 'Oficina Config')
            ->assertJsonPath('data.currency', 'BRL')
            ->assertJsonPath('data.timezone', 'America/Sao_Paulo')
            ->assertJsonPath('data.allow_negative_stock', false)
            ->assertJsonPath('data.auto_deduct_stock_on_service_order_finish', true)
            ->assertJsonPath('data.minimum_stock_default', 0)
            ->assertJsonPath('data.plan', 'starter')
            ->assertJsonPath('data.user_limit', 3);

        $this->assertDatabaseHas('workshop_settings', [
            'tenant_id' => $tenantId,
            'display_name' => 'Oficina Config',
            'currency' => 'BRL',
        ]);
    }

    public function test_it_updates_workshop_settings_for_current_tenant(): void
    {
        $tenantId = $this->createTenant();
        $otherTenantId = $this->createTenant('Outra Oficina');

        DB::table('workshop_settings')->insert([
            'id' => fake()->uuid(),
            'tenant_id' => $otherTenantId,
            'display_name' => 'Outra Oficina',
            'timezone' => 'America/Sao_Paulo',
            'currency' => 'BRL',
            'allow_negative_stock' => false,
            'auto_deduct_stock_on_service_order_finish' => true,
            'minimum_stock_default' => 0,
            'notify_minimum_stock' => true,
            'notify_zero_stock' => true,
            'plan' => 'starter',
            'user_limit' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withHeaders($this->authHeaders($tenantId))
            ->patchJson('/api/v1/settings/workshop', [
                'display_name' => ' Oficina Premium ',
                'legal_name' => 'Oficina Premium LTDA',
                'document' => '12.345.678/0001-90',
                'phone' => '(11) 99999-0000',
                'email' => 'CONTATO@OFICINA.TEST',
                'address' => 'Rua Central, 10',
                'timezone' => 'America/Sao_Paulo',
                'currency' => 'BRL',
                'allow_negative_stock' => true,
                'auto_deduct_stock_on_service_order_finish' => false,
                'minimum_stock_default' => 4,
                'notify_minimum_stock' => false,
                'notify_zero_stock' => true,
                'notification_email' => 'ALERTAS@OFICINA.TEST',
                'notification_phone' => '(11) 98888-7777',
            ])
            ->assertOk()
            ->assertJsonPath('data.tenant_id', $tenantId)
            ->assertJsonPath('data.display_name', 'Oficina Premium')
            ->assertJsonPath('data.document', '12345678000190')
            ->assertJsonPath('data.phone', '11999990000')
            ->assertJsonPath('data.email', 'contato@oficina.test')
            ->assertJsonPath('data.allow_negative_stock', true)
            ->assertJsonPath('data.auto_deduct_stock_on_service_order_finish', false)
            ->assertJsonPath('data.minimum_stock_default', 4)
            ->assertJsonPath('data.notification_email', 'alertas@oficina.test')
            ->assertJsonPath('data.notification_phone', '11988887777')
            ->assertJsonPath('data.plan', 'starter')
            ->assertJsonPath('data.user_limit', 3);

        $this->assertDatabaseHas('workshop_settings', [
            'tenant_id' => $tenantId,
            'display_name' => 'Oficina Premium',
            'document' => '12345678000190',
            'phone' => '11999990000',
            'email' => 'contato@oficina.test',
        ]);

        $this->assertDatabaseHas('workshop_settings', [
            'tenant_id' => $otherTenantId,
            'display_name' => 'Outra Oficina',
        ]);
    }

    public function test_it_rejects_invalid_payload(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId))
            ->patchJson('/api/v1/settings/workshop', [
                'display_name' => '',
                'email' => 'invalid',
                'timezone' => 'Invalid/Timezone',
                'currency' => 'USD',
                'allow_negative_stock' => 'invalid',
                'auto_deduct_stock_on_service_order_finish' => true,
                'minimum_stock_default' => -1,
                'notify_minimum_stock' => true,
                'notify_zero_stock' => true,
                'notification_email' => 'invalid',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'display_name',
                'email',
                'timezone',
                'currency',
                'allow_negative_stock',
                'minimum_stock_default',
                'notification_email',
            ]);
    }

    public function test_mechanic_cannot_manage_workshop_settings(): void
    {
        $tenantId = $this->createTenant();

        $this->withHeaders($this->authHeaders($tenantId, role: 'mechanic'))
            ->getJson('/api/v1/settings/workshop')
            ->assertForbidden();
    }

    private function createTenant(string $name = 'Oficina Teste'): string
    {
        $tenantId = fake()->uuid();

        DB::table('tenants')->insert([
            'id' => $tenantId,
            'name' => $name,
            'document' => '12345678000190',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $tenantId;
    }
}
