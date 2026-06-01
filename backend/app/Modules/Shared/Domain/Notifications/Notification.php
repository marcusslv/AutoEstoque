<?php

namespace App\Modules\Shared\Domain\Notifications;

final class Notification
{
    /**
     * @var array<int, NotificationError>
     */
    private array $errors = [];

    public function add(string $message, ?string $field = null, ?string $code = null): void
    {
        $this->errors[] = new NotificationError(
            field: $field,
            message: $message,
            code: $code,
        );
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    /**
     * @return array<int, NotificationError>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return array<int, array{field: string|null, message: string, code: string|null}>
     */
    public function toArray(): array
    {
        return array_map(
            fn (NotificationError $error): array => [
                'field' => $error->field,
                'message' => $error->message,
                'code' => $error->code,
            ],
            $this->errors,
        );
    }
}
