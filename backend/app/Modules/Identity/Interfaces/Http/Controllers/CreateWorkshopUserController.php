<?php

namespace App\Modules\Identity\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\CreateWorkshopUserUseCase;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\CreateWorkshopUserInput;
use App\Modules\Identity\Domain\Exceptions\DuplicatedUserEmailException;
use App\Modules\Identity\Domain\Exceptions\UserLimitReachedException;
use App\Modules\Identity\Interfaces\Http\Presenters\WorkshopUserPresenter;
use App\Modules\Identity\Interfaces\Http\Requests\CreateWorkshopUserRequest;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CreateWorkshopUserController extends Controller
{
    public function __invoke(
        CreateWorkshopUserRequest $request,
        TenantContext $tenantContext,
        CreateWorkshopUserUseCase $useCase,
        WorkshopUserPresenter $presenter,
    ): JsonResponse {
        try {
            $output = $useCase->execute(new CreateWorkshopUserInput(
                tenantId: $tenantContext->id()->value,
                name: $request->string('name')->toString(),
                email: $request->string('email')->toString(),
                password: $request->string('password')->toString(),
                role: $request->string('role')->toString(),
                status: $request->string('status', 'active')->toString(),
            ));

            return $presenter->present($output);
        } catch (DuplicatedUserEmailException|UserLimitReachedException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_CONFLICT);
        }
    }
}
