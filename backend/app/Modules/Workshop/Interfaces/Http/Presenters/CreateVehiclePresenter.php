<?php

namespace App\Modules\Workshop\Interfaces\Http\Presenters;

use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use App\Modules\Workshop\Application\UseCases\CreateVehicle\Dtos\CreateVehicleOutput;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CreateVehiclePresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof CreateVehicleOutput);

        return response()->json([
            'data' => [
                'id' => $output->id,
                'tenant_id' => $output->tenantId,
                'plate' => $output->plate,
                'brand' => $output->brand,
                'model' => $output->model,
                'year' => $output->year,
                'owner_name' => $output->ownerName,
                'owner_phone' => $output->ownerPhone,
            ],
        ], Response::HTTP_CREATED);
    }
}
