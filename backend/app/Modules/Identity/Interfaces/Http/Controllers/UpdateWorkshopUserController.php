<?php

namespace App\Modules\Identity\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\UpdateWorkshopUserInput;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\UpdateWorkshopUserUseCase;
use App\Modules\Identity\Domain\Exceptions\UserLimitReachedException;
use App\Modules\Identity\Domain\Exceptions\UserNotFoundException;
use App\Modules\Identity\Interfaces\Http\Presenters\WorkshopUserPresenter;
use App\Modules\Identity\Interfaces\Http\Requests\UpdateWorkshopUserRequest;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class UpdateWorkshopUserController extends Controller
{
    public function __invoke(
        string $user,
        UpdateWorkshopUserRequest $request,
        TenantContext $tenantContext,
        UpdateWorkshopUserUseCase $useCase,
        WorkshopUserPresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new UpdateWorkshopUserInput(
                tenantId: $tenantContext->id()->value,
                userId: $user,
                name: $request->string('name')->toString(),
                role: $request->string('role')->toString(),
                status: $request->string('status')->toString(),
            ));

            return $presenter->present($output)->setStatusCode(Response::HTTP_OK);
        } catch (UserNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (UserLimitReachedException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_CONFLICT);
        }
    }
}
