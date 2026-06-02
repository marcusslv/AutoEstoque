<?php

namespace App\Modules\Identity\Infrastructure\Persistence\Eloquent;

use App\Modules\Identity\Application\UseCases\LogoutUser\Contracts\AccessTokenRevoker;
use App\Modules\Identity\Infrastructure\Persistence\Eloquent\Models\UserAccessTokenModel;

final class EloquentAccessTokenRevoker implements AccessTokenRevoker
{
    public function revoke(string $plainToken): void
    {
        UserAccessTokenModel::query()
            ->where('token_hash', hash('sha256', $plainToken))
            ->delete();
    }
}
