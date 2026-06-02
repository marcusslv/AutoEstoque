<?php

namespace App\Modules\Identity\Application\UseCases\RequestPasswordReset\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class RequestPasswordResetInput implements InputDto
{
    public function __construct(public string $email) {}
}
