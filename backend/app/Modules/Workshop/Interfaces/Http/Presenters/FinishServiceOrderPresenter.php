<?php

namespace App\Modules\Workshop\Interfaces\Http\Presenters;

use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use App\Modules\Workshop\Application\UseCases\FinishServiceOrder\Dtos\FinishServiceOrderOutput;
use Illuminate\Http\JsonResponse;

final class FinishServiceOrderPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof FinishServiceOrderOutput);

        return response()->json([
            'data' => [
                'id' => $output->id,
                'tenant_id' => $output->tenantId,
                'status' => $output->status,
                'finished_at' => $output->finishedAt,
                'movement_ids' => $output->movementIds,
            ],
        ]);
    }
}
