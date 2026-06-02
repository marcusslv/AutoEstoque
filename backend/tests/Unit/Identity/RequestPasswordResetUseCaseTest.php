<?php

namespace Tests\Unit\Identity;

use App\Modules\Identity\Application\UseCases\RequestPasswordReset\Contracts\PasswordResetLinkSender;
use App\Modules\Identity\Application\UseCases\RequestPasswordReset\Dtos\RequestPasswordResetInput;
use App\Modules\Identity\Application\UseCases\RequestPasswordReset\Dtos\RequestPasswordResetOutput;
use App\Modules\Identity\Application\UseCases\RequestPasswordReset\RequestPasswordResetUseCase;
use PHPUnit\Framework\TestCase;

class RequestPasswordResetUseCaseTest extends TestCase
{
    public function test_it_requests_password_reset(): void
    {
        $sender = new RequestPasswordResetFakeSender;

        $output = (new RequestPasswordResetUseCase($sender))->execute(new RequestPasswordResetInput(
            email: 'admin@oficina.com',
        ));

        $this->assertInstanceOf(RequestPasswordResetOutput::class, $output);
        $this->assertSame('admin@oficina.com', $sender->lastEmail);
        $this->assertSame('If this email is registered, password reset instructions will be sent.', $output->message);
    }
}

final class RequestPasswordResetFakeSender implements PasswordResetLinkSender
{
    public ?string $lastEmail = null;

    public function send(string $email): void
    {
        $this->lastEmail = $email;
    }
}
