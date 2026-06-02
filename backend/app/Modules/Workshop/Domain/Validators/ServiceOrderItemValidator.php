<?php

namespace App\Modules\Workshop\Domain\Validators;

use App\Modules\Workshop\Domain\Entities\ServiceOrderItem;

final class ServiceOrderItemValidator
{
    public static function validate(ServiceOrderItem $item): void
    {
        if (trim($item->addedByUserId()) === '') {
            $item->notification()->add(
                message: 'Added by user id is required.',
                field: 'added_by_user_id',
                code: 'service_order_item.added_by_user_required',
            );
        }

        if ($item->quantity() <= 0) {
            $item->notification()->add(
                message: 'Quantity must be greater than zero.',
                field: 'quantity',
                code: 'service_order_item.quantity_invalid',
            );
        }
    }
}
