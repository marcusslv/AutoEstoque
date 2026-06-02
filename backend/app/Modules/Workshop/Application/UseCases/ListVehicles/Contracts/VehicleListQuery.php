<?php

namespace App\Modules\Workshop\Application\UseCases\ListVehicles\Contracts;

use App\Modules\Workshop\Application\UseCases\ListVehicles\Dtos\ListVehiclesInput;
use App\Modules\Workshop\Application\UseCases\ListVehicles\Dtos\VehicleOutput;

interface VehicleListQuery
{
    /**
     * @return array<int, VehicleOutput>
     */
    public function search(ListVehiclesInput $input): array;
}
