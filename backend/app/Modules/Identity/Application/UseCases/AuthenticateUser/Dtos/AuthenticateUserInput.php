<?php

namespace App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class AuthenticateUserInput implements InputDto
{
    public function __construct(
        public string $email,
        public string $password,
        public string $tokenName = 'api',
    ) {}
}
