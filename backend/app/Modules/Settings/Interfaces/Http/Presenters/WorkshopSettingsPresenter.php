<?php

namespace App\Modules\Settings\Interfaces\Http\Presenters;

use App\Modules\Settings\Application\UseCases\GetWorkshopSettings\Dtos\WorkshopSettingsOutput;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;

final class WorkshopSettingsPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof WorkshopSettingsOutput);

        return response()->json([
            'data' => [
                'id' => $output->id,
                'tenant_id' => $output->tenantId,
                'display_name' => $output->displayName,
                'legal_name' => $output->legalName,
                'document' => $output->document,
                'phone' => $output->phone,
                'email' => $output->email,
                'address' => $output->address,
                'timezone' => $output->timezone,
                'currency' => $output->currency,
                'allow_negative_stock' => $output->allowNegativeStock,
                'auto_deduct_stock_on_service_order_finish' => $output->autoDeductStockOnServiceOrderFinish,
                'minimum_stock_default' => $output->minimumStockDefault,
                'notify_minimum_stock' => $output->notifyMinimumStock,
                'notify_zero_stock' => $output->notifyZeroStock,
                'notification_email' => $output->notificationEmail,
                'notification_phone' => $output->notificationPhone,
                'plan' => $output->plan,
                'user_limit' => $output->userLimit,
                'updated_at' => $output->updatedAt,
            ],
        ]);
    }
}
