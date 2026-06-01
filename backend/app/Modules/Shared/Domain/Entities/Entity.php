<?php

namespace App\Modules\Shared\Domain\Entities;

use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use App\Modules\Shared\Domain\Notifications\Notification;

abstract class Entity
{
    protected Notification $notification;

    public function __construct()
    {
        $this->notification = new Notification;
    }

    public function notification(): Notification
    {
        return $this->notification;
    }

    protected function throwIfNotificationHasErrors(): void
    {
        if ($this->notification->hasErrors()) {
            throw new DomainValidationException($this->notification);
        }
    }
}
