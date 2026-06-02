<?php

namespace App\Modules\Identity\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\AuthenticateUserUseCase;
use App\Modules\Identity\Application\UseCases\AuthenticateUser\Dtos\AuthenticateUserInput;
use App\Modules\Identity\Domain\Exceptions\InactiveUserException;
use App\Modules\Identity\Domain\Exceptions\InvalidCredentialsException;
use App\Modules\Identity\Domain\Exceptions\UserWithoutTenantException;
use App\Modules\Identity\Interfaces\Http\Presenters\AuthenticateUserPresenter;
use App\Modules\Identity\Interfaces\Http\Requests\AuthenticateUserRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class AuthenticateUserController extends Controller
{
    public function __invoke(
        AuthenticateUserRequest $request,
        AuthenticateUserUseCase $useCase,
        AuthenticateUserPresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new AuthenticateUserInput(
                email: $request->string('email')->toString(),
                password: $request->string('password')->toString(),
                tokenName: $request->string('token_name', 'api')->toString(),
            ));

            return $presenter->present($output);
        } catch (InvalidCredentialsException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);
        } catch (InactiveUserException|UserWithoutTenantException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_FORBIDDEN);
        }
    }
}
