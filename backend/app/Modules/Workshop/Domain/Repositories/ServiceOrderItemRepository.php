<?php

namespace App\Modules\Workshop\Domain\Repositories;

use App\Modules\Workshop\Domain\Entities\ServiceOrderItem;

interface ServiceOrderItemRepository
{
    public function save(ServiceOrderItem $item): void;
}
