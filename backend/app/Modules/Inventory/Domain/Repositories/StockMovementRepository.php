<?php

namespace App\Modules\Inventory\Domain\Repositories;

use App\Modules\Inventory\Domain\Entities\StockMovement;

interface StockMovementRepository
{
    public function save(StockMovement $movement): void;
}
