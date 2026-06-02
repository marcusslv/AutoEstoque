<?php

namespace App\Modules\Workshop\Interfaces\Http\Presenters;

use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use App\Modules\Workshop\Application\UseCases\ListVehicles\Dtos\ListVehiclesOutput;
use App\Modules\Workshop\Application\UseCases\ListVehicles\Dtos\VehicleOutput;
use Illuminate\Http\JsonResponse;

final class ListVehiclesPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof ListVehiclesOutput);

        return response()->json([
            'data' => array_map(
                fn (VehicleOutput $vehicle): array => [
                    'id' => $vehicle->id,
                    'tenant_id' => $vehicle->tenantId,
                    'plate' => $vehicle->plate,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'year' => $vehicle->year,
                    'owner_name' => $vehicle->ownerName,
                    'owner_phone' => $vehicle->ownerPhone,
                    'created_at' => $vehicle->createdAt,
                ],
                $output->vehicles,
            ),
            'meta' => [
                'total' => $output->total(),
            ],
        ]);
    }
}
