<?php

namespace App\Modules\Workshop\Interfaces\Http\Presenters;

use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use App\Modules\Workshop\Application\UseCases\CreateServiceOrder\Dtos\CreateServiceOrderOutput;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CreateServiceOrderPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof CreateServiceOrderOutput);

        return response()->json([
            'data' => [
                'id' => $output->id,
                'tenant_id' => $output->tenantId,
                'vehicle_id' => $output->vehicleId,
                'created_by_user_id' => $output->createdByUserId,
                'customer_name' => $output->customerName,
                'services_description' => $output->servicesDescription,
                'observations' => $output->observations,
                'status' => $output->status,
                'opened_at' => $output->openedAt,
            ],
        ], Response::HTTP_CREATED);
    }
}
