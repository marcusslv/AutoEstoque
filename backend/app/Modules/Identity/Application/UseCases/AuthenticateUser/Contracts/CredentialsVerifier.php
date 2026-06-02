<?php

namespace App\Modules\Identity\Application\UseCases\AuthenticateUser\Contracts;

use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\AuthenticatedIdentity;

interface CredentialsVerifier
{
    public function verify(string $email, string $password): AuthenticatedIdentity;
}
