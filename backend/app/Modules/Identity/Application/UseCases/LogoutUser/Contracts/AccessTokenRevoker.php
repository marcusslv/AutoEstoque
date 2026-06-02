<?php

namespace App\Modules\Identity\Application\UseCases\LogoutUser\Contracts;

interface AccessTokenRevoker
{
    public function revoke(string $plainToken): void;
}
