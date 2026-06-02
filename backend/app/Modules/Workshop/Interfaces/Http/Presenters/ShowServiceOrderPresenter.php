<?php

namespace App\Modules\Workshop\Interfaces\Http\Presenters;

use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos\ServiceOrderPartOutput;
use App\Modules\Workshop\Application\UseCases\ShowServiceOrder\Dtos\ShowServiceOrderOutput;
use Illuminate\Http\JsonResponse;

final class ShowServiceOrderPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof ShowServiceOrderOutput);

        return response()->json([
            'data' => [
                'id' => $output->id,
                'tenant_id' => $output->tenantId,
                'created_by_user_id' => $output->createdByUserId,
                'customer_name' => $output->customerName,
                'services_description' => $output->servicesDescription,
                'observations' => $output->observations,
                'status' => $output->status,
                'opened_at' => $output->openedAt,
                'finished_at' => $output->finishedAt,
                'vehicle' => [
                    'id' => $output->vehicle->id,
                    'plate' => $output->vehicle->plate,
                    'brand' => $output->vehicle->brand,
                    'model' => $output->vehicle->model,
                    'year' => $output->vehicle->year,
                    'owner_name' => $output->vehicle->ownerName,
                    'owner_phone' => $output->vehicle->ownerPhone,
                ],
                'parts' => array_map(
                    fn (ServiceOrderPartOutput $part): array => [
                        'id' => $part->id,
                        'product_id' => $part->productId,
                        'product_name' => $part->productName,
                        'product_sku' => $part->productSku,
                        'added_by_user_id' => $part->addedByUserId,
                        'quantity' => $part->quantity,
                        'created_at' => $part->createdAt,
                    ],
                    $output->parts,
                ),
            ],
            'meta' => [
                'parts_total' => $output->partsTotal(),
            ],
        ]);
    }
}
