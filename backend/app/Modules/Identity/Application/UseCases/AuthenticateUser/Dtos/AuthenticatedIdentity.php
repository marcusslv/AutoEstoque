<?php

namespace App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos;

final readonly class AuthenticatedIdentity
{
    public function __construct(
        public string $userId,
        public string $name,
        public string $email,
        public string $tenantId,
        public string $role,
    ) {}
}
