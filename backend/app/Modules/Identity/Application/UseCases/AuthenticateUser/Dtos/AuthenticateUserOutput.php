<?php

namespace App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class AuthenticateUserOutput implements OutputDto
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public string $userId,
        public string $userName,
        public string $userEmail,
        public string $tenantId,
        public string $role,
    ) {}
}
