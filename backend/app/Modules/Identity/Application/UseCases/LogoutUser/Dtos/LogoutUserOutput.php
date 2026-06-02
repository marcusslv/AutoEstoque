<?php

namespace App\Modules\Identity\Application\UseCases\LogoutUser\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class LogoutUserOutput implements OutputDto
{
    public function __construct(public string $message) {}
}
