<?php

namespace App\Modules\Shared\Domain\Notifications;

final readonly class NotificationError
{
    public function __construct(
        public ?string $field,
        public string $message,
        public ?string $code = null,
    ) {}
}
