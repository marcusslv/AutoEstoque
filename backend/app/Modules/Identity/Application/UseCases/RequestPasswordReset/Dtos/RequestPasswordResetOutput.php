<?php

namespace App\Modules\Identity\Application\UseCases\RequestPasswordReset\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class RequestPasswordResetOutput implements OutputDto
{
    public function __construct(public string $message) {}
}
