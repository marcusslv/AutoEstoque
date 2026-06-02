<?php

namespace App\Modules\Identity\Infrastructure\Persistence\Eloquent;

use App\Models\User;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Contracts\CredentialsVerifier;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\AuthenticatedIdentity;
use App\Modules\Identity\Domain\Exceptions\InactiveUserException;
use App\Modules\Identity\Domain\Exceptions\InvalidCredentialsException;
use App\Modules\Identity\Domain\Exceptions\UserWithoutTenantException;
use Illuminate\Support\Facades\Hash;

final class EloquentCredentialsVerifier implements CredentialsVerifier
{
    public function verify(string $email, string $password): AuthenticatedIdentity
    {
        $user = User::query()
            ->where('email', mb_strtolower(trim($email)))
            ->first();

        if (! $user instanceof User || ! Hash::check($password, (string) $user->password)) {
            throw new InvalidCredentialsException;
        }

        if ($user->status !== 'active') {
            throw new InactiveUserException;
        }

        if ($user->tenant_id === null) {
            throw new UserWithoutTenantException;
        }

        return new AuthenticatedIdentity(
            userId: (string) ($user->public_id ?? $user->id),
            name: (string) $user->name,
            email: (string) $user->email,
            tenantId: (string) $user->tenant_id,
            role: (string) $user->role,
        );
    }
}
