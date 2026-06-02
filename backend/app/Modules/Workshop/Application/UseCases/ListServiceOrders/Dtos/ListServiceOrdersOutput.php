<?php

namespace App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class ListServiceOrdersOutput implements OutputDto
{
    /**
     * @param  array<int, ServiceOrderListItemOutput>  $serviceOrders
     */
    public function __construct(public array $serviceOrders) {}

    public function total(): int
    {
        return count($this->serviceOrders);
    }
}
