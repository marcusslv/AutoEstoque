<?php

namespace App\Modules\Identity\Infrastructure\Persistence\Eloquent;

use App\Models\User;
use App\Modules\Identity\Application\UseCases\ResetPassword\Contracts\PasswordResetter;
use App\Modules\Identity\Domain\Exceptions\PasswordResetFailedException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

final class LaravelPasswordResetter implements PasswordResetter
{
    public function reset(string $email, string $token, string $password): void
    {
        $status = Password::reset([
            'email' => mb_strtolower(trim($email)),
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password,
        ], function (User $user) use ($password): void {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw new PasswordResetFailedException;
        }
    }
}
