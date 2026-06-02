<?php

namespace App\Modules\Identity\Application\UseCases\LogoutUser\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class LogoutUserInput implements InputDto
{
    public function __construct(public string $plainToken) {}
}
