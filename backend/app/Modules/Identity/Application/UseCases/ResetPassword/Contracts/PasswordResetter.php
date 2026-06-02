<?php

namespace App\Modules\Identity\Application\UseCases\ResetPassword\Contracts;

interface PasswordResetter
{
    public function reset(string $email, string $token, string $password): void;
}
