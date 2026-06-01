<?php

namespace App\Modules\Catalog\Domain\Exceptions;

use InvalidArgumentException;

final class InvalidProductException extends InvalidArgumentException
{
    public static function missingName(): self
    {
        return new self('Product name is required.');
    }

    public static function negativeMinimumStock(): self
    {
        return new self('Minimum stock cannot be negative.');
    }
}
