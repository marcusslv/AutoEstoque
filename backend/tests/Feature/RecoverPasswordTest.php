<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class RecoverPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requests_password_reset_for_registered_user(): void
    {
        Notification::fake();
        $tenantId = $this->createTenant();
        $this->createUser($tenantId);

        $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'admin@oficina.com',
        ])->assertOk()
            ->assertJson([
                'message' => 'If this email is registered, password reset instructions will be sent.',
            ]);

        Notification::assertSentTo(
            User::query()->where('email', 'admin@oficina.com')->firstOrFail(),
            ResetPassword::class,
        );
    }

    public function test_it_does_not_reveal_unknown_email_on_password_reset_request(): void
    {
        Notification::fake();

        $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'unknown@oficina.com',
        ])->assertOk()
            ->assertJson([
                'message' => 'If this email is registered, password reset instructions will be sent.',
            ]);

        Notification::assertNothingSent();
    }

    public function test_it_resets_password_with_valid_token(): void
    {
        $tenantId = $this->createTenant();
        $this->createUser($tenantId);
        $user = User::query()->where('email', 'admin@oficina.com')->firstOrFail();
        $token = Password::broker()->createToken($user);

        $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'admin@oficina.com',
            'token' => $token,
            'password' => 'new-secret',
            'password_confirmation' => 'new-secret',
        ])->assertOk()
            ->assertJson([
                'message' => 'Password has been reset.',
            ]);

        $user->refresh();

        $this->assertTrue(Hash::check('new-secret', $user->password));
    }

    public function test_it_rejects_invalid_reset_token(): void
    {
        $tenantId = $this->createTenant();
        $this->createUser($tenantId);

        $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'admin@oficina.com',
            'token' => 'invalid',
            'password' => 'new-secret',
            'password_confirmation' => 'new-secret',
        ])->assertUnprocessable()
            ->assertJson([
                'message' => 'Unable to reset password with the provided token.',
            ]);
    }

    public function test_it_rejects_invalid_payloads(): void
    {
        $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'invalid',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'invalid',
            'token' => '',
            'password' => 'short',
            'password_confirmation' => 'different',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
                'token',
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

    private function createUser(?string $tenantId): int
    {
        return (int) DB::table('users')->insertGetId([
            'tenant_id' => $tenantId,
            'name' => 'Admin Oficina',
            'email' => 'admin@oficina.com',
            'password' => Hash::make('secret'),
            'status' => 'active',
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
