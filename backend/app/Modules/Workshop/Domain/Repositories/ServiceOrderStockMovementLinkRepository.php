<?php

namespace App\Modules\Workshop\Domain\Repositories;

use App\Modules\Workshop\Domain\Entities\ServiceOrderStockMovementLink;

interface ServiceOrderStockMovementLinkRepository
{
    public function save(ServiceOrderStockMovementLink $link): void;
}
