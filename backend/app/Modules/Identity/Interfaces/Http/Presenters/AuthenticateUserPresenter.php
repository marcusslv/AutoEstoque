<?php

namespace App\Modules\Identity\Interfaces\Http\Presenters;

use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\AuthenticateUserOutput;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;

final class AuthenticateUserPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof AuthenticateUserOutput);

        return response()->json([
            'data' => [
                'access_token' => $output->accessToken,
                'token_type' => $output->tokenType,
                'user' => [
                    'id' => $output->userId,
                    'name' => $output->userName,
                    'email' => $output->userEmail,
                    'role' => $output->role,
                ],
                'tenant' => [
                    'id' => $output->tenantId,
                ],
            ],
        ]);
    }
}
