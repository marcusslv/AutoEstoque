<?php

namespace App\Modules\Identity\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Application\UseCases\RequestPasswordReset\Dtos\RequestPasswordResetInput;
use App\Modules\Identity\Application\UseCases\RequestPasswordReset\RequestPasswordResetUseCase;
use App\Modules\Identity\Interfaces\Http\Presenters\RequestPasswordResetPresenter;
use App\Modules\Identity\Interfaces\Http\Requests\RequestPasswordResetRequest;
use Illuminate\Http\JsonResponse;

final class RequestPasswordResetController extends Controller
{
    public function __invoke(
        RequestPasswordResetRequest $request,
        RequestPasswordResetUseCase $useCase,
        RequestPasswordResetPresenter $presenter,
    ): JsonResponse {
        $output = $useCase->execute(new RequestPasswordResetInput(
            email: $request->string('email')->toString(),
        ));

        return $presenter->present($output);
    }
}
