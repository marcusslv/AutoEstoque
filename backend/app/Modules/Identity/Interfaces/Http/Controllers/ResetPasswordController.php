<?php

namespace App\Modules\Identity\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Application\UseCases\ResetPassword\Dtos\ResetPasswordInput;
use App\Modules\Identity\Application\UseCases\ResetPassword\ResetPasswordUseCase;
use App\Modules\Identity\Domain\Exceptions\PasswordResetFailedException;
use App\Modules\Identity\Interfaces\Http\Presenters\ResetPasswordPresenter;
use App\Modules\Identity\Interfaces\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ResetPasswordController extends Controller
{
    public function __invoke(
        ResetPasswordRequest $request,
        ResetPasswordUseCase $useCase,
        ResetPasswordPresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new ResetPasswordInput(
                email: $request->string('email')->toString(),
                token: $request->string('token')->toString(),
                password: $request->string('password')->toString(),
            ));

            return $presenter->present($output);
        } catch (PasswordResetFailedException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
