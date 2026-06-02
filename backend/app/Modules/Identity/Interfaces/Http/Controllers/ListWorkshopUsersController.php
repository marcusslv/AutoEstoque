<?php

namespace App\Modules\Identity\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\ListWorkshopUsersInput;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\ListWorkshopUsersUseCase;
use App\Modules\Identity\Interfaces\Http\Presenters\WorkshopUserPresenter;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;

final class ListWorkshopUsersController extends Controller
{
    public function __invoke(
        TenantContext $tenantContext,
        ListWorkshopUsersUseCase $useCase,
        WorkshopUserPresenter $presenter,
    ): JsonResponse {
        $output = $useCase->execute(new ListWorkshopUsersInput(
            tenantId: $tenantContext->id()->value,
        ));

        return $presenter->present($output);
    }
}
