<?php

namespace App\Modules\Identity\Application\UseCases\ResetPassword;

use App\Modules\Identity\Application\UseCases\ResetPassword\Contracts\PasswordResetter;
use App\Modules\Identity\Application\UseCases\ResetPassword\Dtos\ResetPasswordInput;
use App\Modules\Identity\Application\UseCases\ResetPassword\Dtos\ResetPasswordOutput;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;

/**
 * @implements UseCase<ResetPasswordInput, ResetPasswordOutput>
 */
final readonly class ResetPasswordUseCase implements UseCase
{
    public function __construct(private PasswordResetter $resetter) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof ResetPasswordInput);

        $this->resetter->reset($input->email, $input->token, $input->password);

        return new ResetPasswordOutput('Password has been reset.');
    }
}
