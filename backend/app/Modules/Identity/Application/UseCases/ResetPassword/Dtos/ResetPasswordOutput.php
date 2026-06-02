<?php

namespace App\Modules\Identity\Application\UseCases\ResetPassword\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class ResetPasswordOutput implements OutputDto
{
    public function __construct(public string $message) {}
}
