<?php

namespace App\Modules\Workshop\Application\UseCases\ListServiceOrders;

use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Workshop\Application\UseCases\ListServiceOrders\Contracts\ServiceOrderListQuery;
use App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos\ListServiceOrdersInput;
use App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos\ListServiceOrdersOutput;

/**
 * @implements UseCase<ListServiceOrdersInput, ListServiceOrdersOutput>
 */
final readonly class ListServiceOrdersUseCase implements UseCase
{
    public function __construct(private ServiceOrderListQuery $serviceOrders) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof ListServiceOrdersInput);

        return new ListServiceOrdersOutput(
            serviceOrders: $this->serviceOrders->search($input),
        );
    }
}
