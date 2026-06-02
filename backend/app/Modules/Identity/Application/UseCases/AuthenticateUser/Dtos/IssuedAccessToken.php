<?php

namespace App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos;

final readonly class IssuedAccessToken
{
    public function __construct(public string $plainTextToken) {}
}
