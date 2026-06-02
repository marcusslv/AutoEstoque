<?php

namespace App\Modules\Identity\Infrastructure\Persistence\Eloquent;

use App\Modules\Identity\Application\UseCases\RequestPasswordReset\Contracts\PasswordResetLinkSender;
use Illuminate\Support\Facades\Password;

final class LaravelPasswordResetLinkSender implements PasswordResetLinkSender
{
    public function send(string $email): void
    {
        $status = Password::sendResetLink([
            'email' => mb_strtolower(trim($email)),
        ]);

        if ($status === Password::RESET_LINK_SENT || $status === Password::INVALID_USER) {
            return;
        }
    }
}
