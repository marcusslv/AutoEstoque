<?php

namespace App\Modules\Identity\Application\Contexts;

use App\Modules\Identity\Domain\Exceptions\AuthenticatedUserNotResolvedException;

final class AuthenticatedUserContext
{
    private ?string $userId = null;

    private ?string $role = null;

    public function set(string $userId, string $role): void
    {
        $this->userId = $userId;
        $this->role = $role;
    }

    public function id(): string
    {
        if ($this->userId === null) {
            throw new AuthenticatedUserNotResolvedException;
        }

        return $this->userId;
    }

    public function role(): string
    {
        if ($this->role === null) {
            throw new AuthenticatedUserNotResolvedException;
        }

        return $this->role;
    }

    public function hasAnyRole(string ...$roles): bool
    {
        return in_array($this->role(), $roles, true);
    }

    public function clear(): void
    {
        $this->userId = null;
        $this->role = null;
    }
}
