<?php

namespace App\Modules\Identity\Infrastructure\Persistence\Eloquent;

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

        UserAccessTokenModel::query()->create([
            'id' => (string) Str::uuid(),
            'user_id' => $identity->userId,
            'name' => trim($tokenName) === '' ? 'api' : trim($tokenName),
            'token_hash' => hash('sha256', $plainTextToken),
        ]);

        return new IssuedAccessToken($plainTextToken);
    }
}
