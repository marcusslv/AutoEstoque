<?php

namespace App\Modules\Catalog\Domain\Exceptions;

use InvalidArgumentException;

final class InvalidMoneyException extends InvalidArgumentException
{
    public static function negativeAmount(): self
    {
        return new self('Money amount cannot be negative.');
    }

    public static function invalidCurrency(): self
    {
        return new self('Currency must have 3 characters.');
    }
}
