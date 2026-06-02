<?php

namespace App\Modules\Identity\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Application\UseCases\LogoutUser\Dtos\LogoutUserInput;
use App\Modules\Identity\Application\UseCases\LogoutUser\LogoutUserUseCase;
use App\Modules\Identity\Interfaces\Http\Presenters\LogoutUserPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LogoutUserController extends Controller
{
    public function __invoke(
        Request $request,
        LogoutUserUseCase $useCase,
        LogoutUserPresenter $presenter,
    ): JsonResponse {
        $output = $useCase->execute(new LogoutUserInput(
            plainToken: (string) $request->bearerToken(),
        ));

        return $presenter->present($output);
    }
}
