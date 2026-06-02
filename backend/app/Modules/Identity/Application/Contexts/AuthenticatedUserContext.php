<?php

namespace App\Modules\Identity\Application\Contexts;

use App\Modules\Identity\Domain\Exceptions\AuthenticatedUserNotResolvedException;

final class AuthenticatedUserContext
{
    private ?string $userId = null;

    public function set(string $userId): void
    {
        $this->userId = $userId;
    }

    public function id(): string
    {
        if ($this->userId === null) {
            throw new AuthenticatedUserNotResolvedException;
        }

        return $this->userId;
    }

    public function clear(): void
    {
        $this->userId = null;
    }
}
