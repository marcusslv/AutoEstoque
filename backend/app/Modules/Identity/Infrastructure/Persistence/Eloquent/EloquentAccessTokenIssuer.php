<?php

namespace App\Modules\Identity\Infrastructure\Persistence\Eloquent;

use App\Models\User;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Contracts\AccessTokenIssuer;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\AuthenticatedIdentity;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\IssuedAccessToken;
use App\Modules\Identity\Infrastructure\Persistence\Eloquent\Models\UserAccessTokenModel;
use Illuminate\Support\Str;

final class EloquentAccessTokenIssuer implements AccessTokenIssuer
{
    public function issue(AuthenticatedIdentity $identity, string $tokenName): IssuedAccessToken
    {
        $plainTextToken = Str::random(64);
        $userId = User::query()->where('public_id', $identity->userId)->value('id') ?? $identity->userId;

        logger()->info('Issuing access token for user', [
            'user' => $identity,
            'tenant_id' => $identity->tenantId,
            'token_name' => $tokenName,
        ]);

        UserAccessTokenModel::query()->create([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'name' => trim($tokenName) === '' ? 'api' : trim($tokenName),
            'token_hash' => hash('sha256', $plainTextToken),
        ]);

        return new IssuedAccessToken($plainTextToken);
    }
}
