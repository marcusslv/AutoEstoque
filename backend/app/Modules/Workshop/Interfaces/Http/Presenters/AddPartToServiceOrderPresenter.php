<?php

namespace App\Modules\Workshop\Interfaces\Http\Presenters;

use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use App\Modules\Workshop\Application\UseCases\AddPartToServiceOrder\Dtos\AddPartToServiceOrderOutput;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class AddPartToServiceOrderPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof AddPartToServiceOrderOutput);

        return response()->json([
            'data' => [
                'id' => $output->id,
                'tenant_id' => $output->tenantId,
                'service_order_id' => $output->serviceOrderId,
                'product_id' => $output->productId,
                'added_by_user_id' => $output->addedByUserId,
                'quantity' => $output->quantity,
            ],
        ], Response::HTTP_CREATED);
    }
}
