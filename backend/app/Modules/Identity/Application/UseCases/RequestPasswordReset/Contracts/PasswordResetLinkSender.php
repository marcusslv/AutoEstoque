<?php

namespace App\Modules\Identity\Application\UseCases\RequestPasswordReset\Contracts;

interface PasswordResetLinkSender
{
    public function send(string $email): void;
}
