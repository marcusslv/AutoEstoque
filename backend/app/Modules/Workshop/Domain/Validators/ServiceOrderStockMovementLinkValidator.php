<?php

namespace App\Modules\Workshop\Domain\Validators;

use App\Modules\Workshop\Domain\Entities\ServiceOrderStockMovementLink;

final class ServiceOrderStockMovementLinkValidator
{
    public static function validate(ServiceOrderStockMovementLink $link): void
    {
        if (trim($link->stockMovementId()->value) === '') {
            $link->notification()->add(
                message: 'Stock movement id is required.',
                field: 'stock_movement_id',
                code: 'service_order_stock_movement_link.stock_movement_required',
            );
        }
    }
}
