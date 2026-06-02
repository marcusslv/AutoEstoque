<?php

namespace Tests\Unit\Identity;

use App\Modules\Identity\Application\UseCases\AuthenticateUser\AuthenticateUserUseCase;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Contracts\AccessTokenIssuer;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Contracts\CredentialsVerifier;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\AuthenticatedIdentity;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\AuthenticateUserInput;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\AuthenticateUserOutput;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\IssuedAccessToken;
use PHPUnit\Framework\TestCase;

class AuthenticateUserUseCaseTest extends TestCase
{
    public function test_it_authenticates_user_and_issues_access_token(): void
    {
        $credentials = new AuthenticateUserFakeCredentialsVerifier;
        $tokens = new AuthenticateUserFakeTokenIssuer;

        $output = (new AuthenticateUserUseCase($credentials, $tokens))->execute(new AuthenticateUserInput(
            email: 'admin@oficina.com',
            password: 'secret',
            tokenName: 'mobile',
        ));

        $this->assertInstanceOf(AuthenticateUserOutput::class, $output);
        $this->assertSame('plain-token', $output->accessToken);
        $this->assertSame('Bearer', $output->tokenType);
        $this->assertSame('018f95f2-0f08-7f85-9b31-2d833a1a2f42', $output->tenantId);
        $this->assertSame('admin@oficina.com', $credentials->lastEmail);
        $this->assertSame('mobile', $tokens->lastTokenName);
    }
}

final class AuthenticateUserFakeCredentialsVerifier implements CredentialsVerifier
{
    public ?string $lastEmail = null;

    public function verify(string $email, string $password): AuthenticatedIdentity
    {
        $this->lastEmail = $email;

        return new AuthenticatedIdentity(
            userId: '1',
            name: 'Admin Oficina',
            email: $email,
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            role: 'admin',
        );
    }
}

final class AuthenticateUserFakeTokenIssuer implements AccessTokenIssuer
{
    public ?string $lastTokenName = null;

    public function issue(AuthenticatedIdentity $identity, string $tokenName): IssuedAccessToken
    {
        $this->lastTokenName = $tokenName;

        return new IssuedAccessToken('plain-token');
    }
}
