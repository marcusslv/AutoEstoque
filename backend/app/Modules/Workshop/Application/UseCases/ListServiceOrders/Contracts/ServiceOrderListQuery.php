<?php

namespace App\Modules\Workshop\Application\UseCases\ListServiceOrders\Contracts;

use App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos\ListServiceOrdersInput;
use App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos\ServiceOrderListItemOutput;

interface ServiceOrderListQuery
{
    /**
     * @return array<int, ServiceOrderListItemOutput>
     */
    public function search(ListServiceOrdersInput $input): array;
}
