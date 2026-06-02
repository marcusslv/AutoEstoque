<?php

namespace App\Modules\Workshop\Application\UseCases\ListVehicles\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class ListVehiclesOutput implements OutputDto
{
    /**
     * @param  array<int, VehicleOutput>  $vehicles
     */
    public function __construct(public array $vehicles) {}

    public function total(): int
    {
        return count($this->vehicles);
    }
}
