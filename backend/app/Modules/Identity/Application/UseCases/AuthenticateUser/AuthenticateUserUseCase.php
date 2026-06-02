<?php

namespace App\Modules\Identity\Application\UseCases\AuthenticateUser;

use App\Modules\Identity\Application\UseCases\AuthenticateUser\Contracts\AccessTokenIssuer;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Contracts\CredentialsVerifier;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\AuthenticateUserInput;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\AuthenticateUserOutput;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;

/**
 * @implements UseCase<AuthenticateUserInput, AuthenticateUserOutput>
 */
final readonly class AuthenticateUserUseCase implements UseCase
{
    public function __construct(
        private CredentialsVerifier $credentials,
        private AccessTokenIssuer $tokens,
    ) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof AuthenticateUserInput);

        $identity = $this->credentials->verify($input->email, $input->password);
        $token = $this->tokens->issue($identity, $input->tokenName);

        return new AuthenticateUserOutput(
            accessToken: $token->plainTextToken,
            tokenType: 'Bearer',
            userId: $identity->userId,
            userName: $identity->name,
            userEmail: $identity->email,
            tenantId: $identity->tenantId,
            role: $identity->role,
        );
    }
}
