<?php

namespace App\Modules\Inventory\Domain\Validators;

use App\Modules\Inventory\Domain\Entities\StockMovement;

final class StockMovementValidator
{
    public static function validate(StockMovement $movement): void
    {
        if (trim($movement->userId()) === '') {
            $movement->notification()->add(
                field: 'user_id',
                message: 'User id is required.',
                code: 'stock_movement.user_required',
            );
        }
    }
}
