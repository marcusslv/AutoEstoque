<?php

namespace App\Modules\Shared\Domain\Exceptions;

use App\Modules\Shared\Domain\Notifications\Notification;
use RuntimeException;

final class DomainValidationException extends RuntimeException
{
    public function __construct(private readonly Notification $notification)
    {
        parent::__construct('Domain validation failed.');
    }

    public function notification(): Notification
    {
        return $this->notification;
    }

    /**
     * @return array<int, array{field: string|null, message: string, code: string|null}>
     */
    public function errors(): array
    {
        return $this->notification->toArray();
    }
}
