<?php

namespace App\Modules\Workshop\Domain\ValueObjects;

use App\Modules\Workshop\Domain\Exceptions\InvalidServiceOrderStatusException;

final readonly class ServiceOrderStatus
{
    public const OPEN = 'open';

    public const FINISHED = 'finished';

    public function __construct(public string $value)
    {
        if (! in_array($value, [self::OPEN, self::FINISHED], true)) {
            throw new InvalidServiceOrderStatusException;
        }
    }
}
