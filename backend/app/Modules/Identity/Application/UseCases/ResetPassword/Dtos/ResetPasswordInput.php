<?php

namespace App\Modules\Identity\Application\UseCases\ResetPassword\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class ResetPasswordInput implements InputDto
{
    public function __construct(
        public string $email,
        public string $token,
        public string $password,
    ) {}
}
