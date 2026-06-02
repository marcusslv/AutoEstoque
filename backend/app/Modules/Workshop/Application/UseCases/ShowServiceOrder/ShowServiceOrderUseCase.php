<?php

namespace App\Modules\Workshop\Application\UseCases\ShowServiceOrder;

use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Contracts\ServiceOrderDetailsQuery;
use App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos\ShowServiceOrderInput;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotFoundException;

/**
 * @implements UseCase<ShowServiceOrderInput, OutputDto>
 */
final readonly class ShowServiceOrderUseCase implements UseCase
{
    public function __construct(private ServiceOrderDetailsQuery $serviceOrders) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof ShowServiceOrderInput);

        $output = $this->serviceOrders->find($input);

        if ($output === null) {
            throw new ServiceOrderNotFoundException;
        }

        return $output;
    }
}
