<?php

namespace App\Modules\Settings\Application\UseCases\GetWorkshopSettings\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class WorkshopSettingsOutput implements OutputDto
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $displayName,
        public ?string $legalName,
        public ?string $document,
        public ?string $phone,
        public ?string $email,
        public ?string $address,
        public string $timezone,
        public string $currency,
        public bool $allowNegativeStock,
        public bool $autoDeductStockOnServiceOrderFinish,
        public int $minimumStockDefault,
        public bool $notifyMinimumStock,
        public bool $notifyZeroStock,
        public ?string $notificationEmail,
        public ?string $notificationPhone,
        public string $plan,
        public int $userLimit,
        public string $updatedAt,
    ) {}
}
