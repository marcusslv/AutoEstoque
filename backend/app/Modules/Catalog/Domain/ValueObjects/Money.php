<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

use App\Modules\Catalog\Domain\Exceptions\InvalidMoneyException;

final readonly class Money
{
    public function __construct(
        public int $amountInCents,
        public string $currency = 'BRL',
    ) {
        if ($this->amountInCents < 0) {
            throw InvalidMoneyException::negativeAmount();
        }

        if (strlen($this->currency) !== 3) {
            throw InvalidMoneyException::invalidCurrency();
        }
    }
}
