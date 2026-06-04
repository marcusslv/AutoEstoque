<?php

namespace App\Modules\Settings\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Settings\Application\UseCases\GetWorkshopSettings\Dtos\WorkshopSettingsOutput;
use App\Modules\Settings\Application\UseCases\UpdateWorkshopSettings\Dtos\UpdateWorkshopSettingsInput;
use App\Modules\Settings\Domain\Factories\WorkshopSettingsFactory;
use App\Modules\Settings\Domain\Repositories\WorkshopSettingsRepository;
use App\Modules\Settings\Infrastructure\Persistence\Eloquent\WorkshopSettingsModel;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Tenant\Infrastructure\Persistence\Eloquent\TenantModel;
use Illuminate\Support\Str;

final readonly class EloquentWorkshopSettingsRepository implements WorkshopSettingsRepository
{
    public function __construct(private WorkshopSettingsFactory $factory) {}

    public function getOrCreate(TenantId $tenantId): WorkshopSettingsOutput
    {
        $settings = WorkshopSettingsModel::query()
            ->where('tenant_id', $tenantId->value)
            ->first();

        if ($settings instanceof WorkshopSettingsModel) {
            return $this->toOutput($settings);
        }

        $tenant = TenantModel::query()->findOrFail($tenantId->value);

        $settings = WorkshopSettingsModel::query()->create([
            'tenant_id' => $tenantId->value,
            'display_name' => $tenant->name,
            'legal_name' => null,
            'document' => $tenant->document,
            'phone' => null,
            'email' => null,
            'address' => null,
            'timezone' => 'America/Sao_Paulo',
            'currency' => 'BRL',
            'allow_negative_stock' => false,
            'auto_deduct_stock_on_service_order_finish' => true,
            'minimum_stock_default' => 0,
            'notify_minimum_stock' => true,
            'notify_zero_stock' => true,
            'notification_email' => null,
            'notification_phone' => null,
            'plan' => 'starter',
            'user_limit' => 3,
        ]);

        return $this->toOutput($settings);
    }

    public function update(UpdateWorkshopSettingsInput $input): WorkshopSettingsOutput
    {
        $current = $this->getOrCreate(new TenantId($input->tenantId));

        $entity = $this->factory->create(
            id: $current->id !== '' ? $current->id : (string) Str::uuid(),
            tenantId: new TenantId($input->tenantId),
            displayName: $input->displayName,
            legalName: $input->legalName,
            document: $input->document,
            phone: $input->phone,
            email: $input->email,
            address: $input->address,
            timezone: $input->timezone,
            currency: $input->currency,
            allowNegativeStock: $input->allowNegativeStock,
            autoDeductStockOnServiceOrderFinish: $input->autoDeductStockOnServiceOrderFinish,
            minimumStockDefault: $input->minimumStockDefault,
            notifyMinimumStock: $input->notifyMinimumStock,
            notifyZeroStock: $input->notifyZeroStock,
            notificationEmail: $input->notificationEmail,
            notificationPhone: $input->notificationPhone,
            plan: $current->plan,
            userLimit: $current->userLimit,
        );

        $settings = WorkshopSettingsModel::query()
            ->where('tenant_id', $entity->tenantId()->value)
            ->firstOrFail();

        $settings->update([
            'display_name' => $entity->displayName(),
            'legal_name' => $entity->legalName(),
            'document' => $entity->document(),
            'phone' => $entity->phone(),
            'email' => $entity->email(),
            'address' => $entity->address(),
            'timezone' => $entity->timezone(),
            'currency' => $entity->currency(),
            'allow_negative_stock' => $entity->allowNegativeStock(),
            'auto_deduct_stock_on_service_order_finish' => $entity->autoDeductStockOnServiceOrderFinish(),
            'minimum_stock_default' => $entity->minimumStockDefault(),
            'notify_minimum_stock' => $entity->notifyMinimumStock(),
            'notify_zero_stock' => $entity->notifyZeroStock(),
            'notification_email' => $entity->notificationEmail(),
            'notification_phone' => $entity->notificationPhone(),
        ]);

        return $this->toOutput($settings->refresh());
    }

    private function toOutput(WorkshopSettingsModel $settings): WorkshopSettingsOutput
    {
        return new WorkshopSettingsOutput(
            id: (string) $settings->id,
            tenantId: (string) $settings->tenant_id,
            displayName: (string) $settings->display_name,
            legalName: $settings->legal_name,
            document: $settings->document,
            phone: $settings->phone,
            email: $settings->email,
            address: $settings->address,
            timezone: (string) $settings->timezone,
            currency: (string) $settings->currency,
            allowNegativeStock: (bool) $settings->allow_negative_stock,
            autoDeductStockOnServiceOrderFinish: (bool) $settings->auto_deduct_stock_on_service_order_finish,
            minimumStockDefault: (int) $settings->minimum_stock_default,
            notifyMinimumStock: (bool) $settings->notify_minimum_stock,
            notifyZeroStock: (bool) $settings->notify_zero_stock,
            notificationEmail: $settings->notification_email,
            notificationPhone: $settings->notification_phone,
            plan: (string) $settings->plan,
            userLimit: (int) $settings->user_limit,
            updatedAt: $settings->updated_at?->toISOString() ?? '',
        );
    }
}
