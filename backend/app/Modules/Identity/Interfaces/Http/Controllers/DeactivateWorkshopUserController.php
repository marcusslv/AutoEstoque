<?php

namespace App\Modules\Identity\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\DeactivateWorkshopUserUseCase;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\DeactivateWorkshopUserInput;
use App\Modules\Identity\Domain\Exceptions\UserNotFoundException;
use App\Modules\Identity\Interfaces\Http\Presenters\WorkshopUserPresenter;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class DeactivateWorkshopUserController extends Controller
{
    public function __invoke(
        string $user,
        TenantContext $tenantContext,
        DeactivateWorkshopUserUseCase $useCase,
        WorkshopUserPresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new DeactivateWorkshopUserInput(
                tenantId: $tenantContext->id()->value,
                userId: $user,
            ));

            return $presenter->present($output)->setStatusCode(Response::HTTP_OK);
        } catch (UserNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
