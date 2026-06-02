<?php

namespace Tests\Unit\Identity;

use App\Modules\Identity\Application\UseCases\ResetPassword\Contracts\PasswordResetter;
use App\Modules\Identity\Application\UseCases\ResetPassword\Dtos\ResetPasswordInput;
use App\Modules\Identity\Application\UseCases\ResetPassword\Dtos\ResetPasswordOutput;
use App\Modules\Identity\Application\UseCases\ResetPassword\ResetPasswordUseCase;
use PHPUnit\Framework\TestCase;

class ResetPasswordUseCaseTest extends TestCase
{
    public function test_it_resets_password(): void
    {
        $resetter = new ResetPasswordFakeResetter;

        $output = (new ResetPasswordUseCase($resetter))->execute(new ResetPasswordInput(
            email: 'admin@oficina.com',
            token: 'reset-token',
            password: 'new-secret',
        ));

        $this->assertInstanceOf(ResetPasswordOutput::class, $output);
        $this->assertSame('admin@oficina.com', $resetter->lastEmail);
        $this->assertSame('reset-token', $resetter->lastToken);
        $this->assertSame('new-secret', $resetter->lastPassword);
        $this->assertSame('Password has been reset.', $output->message);
    }
}

final class ResetPasswordFakeResetter implements PasswordResetter
{
    public ?string $lastEmail = null;

    public ?string $lastToken = null;

    public ?string $lastPassword = null;

    public function reset(string $email, string $token, string $password): void
    {
        $this->lastEmail = $email;
        $this->lastToken = $token;
        $this->lastPassword = $password;
    }
}
