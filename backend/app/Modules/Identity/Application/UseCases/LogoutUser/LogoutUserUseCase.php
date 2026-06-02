<?php

namespace App\Modules\Identity\Application\UseCases\LogoutUser;

use App\Modules\Identity\Application\UseCases\LogoutUser\Contracts\AccessTokenRevoker;
use App\Modules\Identity\Application\UseCases\LogoutUser\Dtos\LogoutUserInput;
use App\Modules\Identity\Application\UseCases\LogoutUser\Dtos\LogoutUserOutput;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;

/**
 * @implements UseCase<LogoutUserInput, LogoutUserOutput>
 */
final readonly class LogoutUserUseCase implements UseCase
{
    public function __construct(private AccessTokenRevoker $tokens) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof LogoutUserInput);

        $this->tokens->revoke($input->plainToken);

        return new LogoutUserOutput('Logged out successfully.');
    }
}
