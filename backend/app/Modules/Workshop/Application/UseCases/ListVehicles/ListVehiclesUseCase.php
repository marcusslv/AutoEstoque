<?php

namespace App\Modules\Workshop\Application\UseCases\ListVehicles;

use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Workshop\Application\UseCases\ListVehicles\Contracts\VehicleListQuery;
use App\Modules\Workshop\Application\UseCases\ListVehicles\Dtos\ListVehiclesInput;
use App\Modules\Workshop\Application\UseCases\ListVehicles\Dtos\ListVehiclesOutput;

/**
 * @implements UseCase<ListVehiclesInput, ListVehiclesOutput>
 */
final readonly class ListVehiclesUseCase implements UseCase
{
    public function __construct(private VehicleListQuery $vehicles) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof ListVehiclesInput);

        return new ListVehiclesOutput(
            vehicles: $this->vehicles->search($input),
        );
    }
}
