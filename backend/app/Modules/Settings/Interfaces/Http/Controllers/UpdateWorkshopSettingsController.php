<?php

namespace App\Modules\Settings\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Application\UseCases\UpdateWorkshopSettings\Dtos\UpdateWorkshopSettingsInput;
use App\Modules\Settings\Application\UseCases\UpdateWorkshopSettings\UpdateWorkshopSettingsUseCase;
use App\Modules\Settings\Interfaces\Http\Presenters\WorkshopSettingsPresenter;
use App\Modules\Settings\Interfaces\Http\Requests\UpdateWorkshopSettingsRequest;
use App\Modules\Tenant\Application\TenantContext;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class UpdateWorkshopSettingsController extends Controller
{
    public function __invoke(
        UpdateWorkshopSettingsRequest $request,
        TenantContext $tenantContext,
        UpdateWorkshopSettingsUseCase $useCase,
        WorkshopSettingsPresenter $presenter,
    ): JsonResponse {
        $output = $useCase->execute(new UpdateWorkshopSettingsInput(
            tenantId: $tenantContext->id()->value,
            displayName: $request->string('display_name')->toString(),
            legalName: $request->has('legal_name') ? $request->string('legal_name')->toString() : null,
            document: $request->has('document') ? $request->string('document')->toString() : null,
            phone: $request->has('phone') ? $request->string('phone')->toString() : null,
            email: $request->has('email') ? $request->string('email')->toString() : null,
            address: $request->has('address') ? $request->string('address')->toString() : null,
            timezone: $request->string('timezone')->toString(),
            currency: $request->string('currency')->toString(),
            allowNegativeStock: $request->boolean('allow_negative_stock'),
            autoDeductStockOnServiceOrderFinish: $request->boolean('auto_deduct_stock_on_service_order_finish'),
            minimumStockDefault: $request->integer('minimum_stock_default'),
            notifyMinimumStock: $request->boolean('notify_minimum_stock'),
            notifyZeroStock: $request->boolean('notify_zero_stock'),
            notificationEmail: $request->has('notification_email') ? $request->string('notification_email')->toString() : null,
            notificationPhone: $request->has('notification_phone') ? $request->string('notification_phone')->toString() : null,
        ));

        return $presenter->present($output)->setStatusCode(Response::HTTP_OK);
    }
}
