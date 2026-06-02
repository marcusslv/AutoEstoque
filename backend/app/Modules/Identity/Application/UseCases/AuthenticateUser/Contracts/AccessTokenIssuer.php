<?php

namespace App\Modules\Identity\Application\UseCases\AuthenticateUser\Contracts;

use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\AuthenticatedIdentity;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\IssuedAccessToken;

interface AccessTokenIssuer
{
    public function issue(AuthenticatedIdentity $identity, string $tokenName): IssuedAccessToken;
}
