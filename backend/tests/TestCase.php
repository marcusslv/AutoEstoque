<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    /**
     * @return array<string, string>
     */
    protected function authHeaders(string $tenantId, ?string $publicUserId = null): array
    {
        $plainToken = Str::random(64);

        if (! DB::table('tenants')->where('id', $tenantId)->exists()) {
            DB::table('tenants')->insert([
                'id' => $tenantId,
                'name' => 'Oficina Teste',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($publicUserId === null) {
            $existingUser = DB::table('users')
                ->where('tenant_id', $tenantId)
                ->where('email', 'like', 'auth-user-%')
                ->first();

            $publicUserId = $existingUser?->public_id;
        }

        $publicUserId ??= (string) Str::uuid();

        $userId = DB::table('users')->where('public_id', $publicUserId)->value('id');

        if ($userId === null) {
            $userId = DB::table('users')->insertGetId([
                'tenant_id' => $tenantId,
                'public_id' => $publicUserId,
                'name' => 'Usuario Teste',
                'email' => 'auth-user-'.Str::uuid().'@oficina.com',
                'password' => Hash::make('secret'),
                'status' => 'active',
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('user_access_tokens')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'name' => 'test',
            'token_hash' => hash('sha256', $plainToken),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'Authorization' => "Bearer {$plainToken}",
        ];
    }
}
