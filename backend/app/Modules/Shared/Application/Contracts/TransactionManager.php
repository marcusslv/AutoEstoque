<?php

namespace App\Modules\Shared\Application\Contracts;

interface TransactionManager
{
    public function run(callable $callback): mixed;
}
