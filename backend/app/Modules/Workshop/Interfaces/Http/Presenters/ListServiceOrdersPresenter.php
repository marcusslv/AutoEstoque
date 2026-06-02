<?php

namespace App\Modules\Workshop\Interfaces\Http\Presenters;

use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos\ListServiceOrdersOutput;
use App\Modules\Workshop\Application\UseCases\ListServiceOrders\Dtos\ServiceOrderListItemOutput;
use Illuminate\Http\JsonResponse;

final class ListServiceOrdersPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof ListServiceOrdersOutput);

        return response()->json([
            'data' => array_map(
                fn (ServiceOrderListItemOutput $serviceOrder): array => [
                    'id' => $serviceOrder->id,
                    'tenant_id' => $serviceOrder->tenantId,
                    'customer_name' => $serviceOrder->customerName,
                    'services_description' => $serviceOrder->servicesDescription,
                    'observations' => $serviceOrder->observations,
                    'status' => $serviceOrder->status,
                    'opened_at' => $serviceOrder->openedAt,
                    'finished_at' => $serviceOrder->finishedAt,
                    'vehicle' => [
                        'id' => $serviceOrder->vehicle->id,
                        'plate' => $serviceOrder->vehicle->plate,
                        'brand' => $serviceOrder->vehicle->brand,
                        'model' => $serviceOrder->vehicle->model,
                        'owner_name' => $serviceOrder->vehicle->ownerName,
                    ],
                    'parts_total' => $serviceOrder->partsTotal,
                ],
                $output->serviceOrders,
            ),
            'meta' => [
                'total' => $output->total(),
            ],
        ]);
    }
}
