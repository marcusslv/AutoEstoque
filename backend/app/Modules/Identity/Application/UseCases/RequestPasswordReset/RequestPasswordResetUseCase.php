<?php

namespace App\Modules\Identity\Application\UseCases\RequestPasswordReset;

use App\Modules\Identity\Application\UseCases\RequestPasswordReset\Contracts\PasswordResetLinkSender;
use App\Modules\Identity\Application\UseCases\RequestPasswordReset\Dtos\RequestPasswordResetInput;
use App\Modules\Identity\Application\UseCases\RequestPasswordReset\Dtos\RequestPasswordResetOutput;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;

/**
 * @implements UseCase<RequestPasswordResetInput, RequestPasswordResetOutput>
 */
final readonly class RequestPasswordResetUseCase implements UseCase
{
    public function __construct(private PasswordResetLinkSender $sender) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof RequestPasswordResetInput);

        $this->sender->send($input->email);

        return new RequestPasswordResetOutput(
            message: 'If this email is registered, password reset instructions will be sent.',
        );
    }
}
