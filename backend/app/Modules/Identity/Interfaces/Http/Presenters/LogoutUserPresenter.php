<?php

namespace App\Modules\Identity\Interfaces\Http\Presenters;

use App\Modules\Identity\Application\UseCases\LogoutUser\Dtos\LogoutUserOutput;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;

final class LogoutUserPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof LogoutUserOutput);

        return response()->json([
            'message' => $output->message,
        ]);
    }
}
