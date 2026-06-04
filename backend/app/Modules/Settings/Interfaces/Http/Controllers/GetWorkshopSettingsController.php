<?php

namespace App\Modules\Settings\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Application\UseCases\GetWorkshopSettings\Dtos\GetWorkshopSettingsInput;
use App\Modules\Settings\Application\UseCases\GetWorkshopSettings\GetWorkshopSettingsUseCase;
use App\Modules\Settings\Interfaces\Http\Presenters\WorkshopSettingsPresenter;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;

final class GetWorkshopSettingsController extends Controller
{
    public function __invoke(
        TenantContext $tenantContext,
        GetWorkshopSettingsUseCase $useCase,
        WorkshopSettingsPresenter $presenter,
    ): JsonResponse {
        $output = $useCase->execute(new GetWorkshopSettingsInput(
            tenantId: $tenantContext->id()->value,
        ));

        return $presenter->present($output);
    }
}
