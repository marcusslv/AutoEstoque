<?php

namespace App\Modules\Settings\Domain\Factories;

use App\Modules\Settings\Domain\Entities\WorkshopSettings;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

final class WorkshopSettingsFactory
{
    public function create(
        string $id,
        TenantId $tenantId,
        string $displayName,
        ?string $legalName,
        ?string $document,
        ?string $phone,
        ?string $email,
        ?string $address,
        string $timezone,
        string $currency,
        bool $allowNegativeStock,
        bool $autoDeductStockOnServiceOrderFinish,
        int $minimumStockDefault,
        bool $notifyMinimumStock,
        bool $notifyZeroStock,
        ?string $notificationEmail,
        ?string $notificationPhone,
        string $plan,
        int $userLimit,
    ): WorkshopSettings {
        return new WorkshopSettings(
            id: $id,
            tenantId: $tenantId,
            displayName: trim($displayName),
            legalName: $this->nullableTrim($legalName),
            document: $this->digitsOnlyOrNull($document),
            phone: $this->digitsOnlyOrNull($phone),
            email: $this->nullableLowerTrim($email),
            address: $this->nullableTrim($address),
            timezone: trim($timezone),
            currency: mb_strtoupper(trim($currency)),
            allowNegativeStock: $allowNegativeStock,
            autoDeductStockOnServiceOrderFinish: $autoDeductStockOnServiceOrderFinish,
            minimumStockDefault: $minimumStockDefault,
            notifyMinimumStock: $notifyMinimumStock,
            notifyZeroStock: $notifyZeroStock,
            notificationEmail: $this->nullableLowerTrim($notificationEmail),
            notificationPhone: $this->digitsOnlyOrNull($notificationPhone),
            plan: trim($plan),
            userLimit: $userLimit,
        );
    }

    private function nullableTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function nullableLowerTrim(?string $value): ?string
    {
        $value = $this->nullableTrim($value);

        return $value === null ? null : mb_strtolower($value);
    }

    private function digitsOnlyOrNull(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = preg_replace('/\D+/', '', $value) ?? '';

        return $value === '' ? null : $value;
    }
}
