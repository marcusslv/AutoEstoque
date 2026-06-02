<?php

namespace App\Modules\Workshop\Domain\Repositories;

use App\Modules\Workshop\Domain\Entities\ServiceOrder;

interface ServiceOrderRepository
{
    public function save(ServiceOrder $serviceOrder): void;
}
