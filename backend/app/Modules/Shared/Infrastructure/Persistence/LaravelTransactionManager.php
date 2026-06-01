<?php

namespace App\Modules\Shared\Infrastructure\Persistence;

use App\Modules\Shared\Application\Contracts\TransactionManager;
use Illuminate\Support\Facades\DB;

final class LaravelTransactionManager implements TransactionManager
{
    public function run(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
