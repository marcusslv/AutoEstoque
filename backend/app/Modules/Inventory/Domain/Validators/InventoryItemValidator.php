<?php

namespace App\Modules\Inventory\Domain\Validators;

use App\Modules\Inventory\Domain\Entities\InventoryItem;

final class InventoryItemValidator
{
    public static function validate(InventoryItem $item): void
    {
        if ($item->currentStock()->value < 0) {
            $item->notification()->add(
                field: 'current_stock',
                message: 'Current stock cannot be negative.',
                code: 'inventory.current_stock_negative',
            );
        }
    }
}
