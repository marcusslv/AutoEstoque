<?php

namespace App\Modules\Workshop\Domain\Validators;

use App\Modules\Workshop\Domain\Entities\ServiceOrder;

final class ServiceOrderValidator
{
    public static function validate(ServiceOrder $serviceOrder): void
    {
        if (trim($serviceOrder->createdByUserId()) === '') {
            $serviceOrder->notification()->add(
                message: 'Created by user id is required.',
                field: 'created_by_user_id',
                code: 'service_order.created_by_user_required',
            );
        }

        if (trim($serviceOrder->customerName()) === '') {
            $serviceOrder->notification()->add(
                message: 'Customer name is required.',
                field: 'customer_name',
                code: 'service_order.customer_name_required',
            );
        }

        if (trim($serviceOrder->servicesDescription()) === '') {
            $serviceOrder->notification()->add(
                message: 'Services description is required.',
                field: 'services_description',
                code: 'service_order.services_description_required',
            );
        }
    }
}
