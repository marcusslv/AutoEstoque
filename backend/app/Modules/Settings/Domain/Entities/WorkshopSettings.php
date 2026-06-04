<?php

namespace App\Modules\Settings\Domain\Entities;

use App\Modules\Settings\Domain\Validators\WorkshopSettingsValidator;
use App\Modules\Shared\Domain\Entities\Entity;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

final class WorkshopSettings extends Entity
{
    public function __construct(
        private readonly string $id,
        private readonly TenantId $tenantId,
        private readonly string $displayName,
        private readonly ?string $legalName,
        private readonly ?string $document,
        private readonly ?string $phone,
        private readonly ?string $email,
        private readonly ?string $address,
        private readonly string $timezone,
        private readonly string $currency,
        private readonly bool $allowNegativeStock,
        private readonly bool $autoDeductStockOnServiceOrderFinish,
        private readonly int $minimumStockDefault,
        private readonly bool $notifyMinimumStock,
        private readonly bool $notifyZeroStock,
        private readonly ?string $notificationEmail,
        private readonly ?string $notificationPhone,
        private readonly string $plan,
        private readonly int $userLimit,
    ) {
        parent::__construct();

        WorkshopSettingsValidator::validate($this);

        $this->throwIfNotificationHasErrors();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function displayName(): string
    {
        return $this->displayName;
    }

    public function legalName(): ?string
    {
        return $this->legalName;
    }

    public function document(): ?string
    {
        return $this->document;
    }

    public function phone(): ?string
    {
        return $this->phone;
    }

    public function email(): ?string
    {
        return $this->email;
    }

    public function address(): ?string
    {
        return $this->address;
    }

    public function timezone(): string
    {
        return $this->timezone;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function allowNegativeStock(): bool
    {
        return $this->allowNegativeStock;
    }

    public function autoDeductStockOnServiceOrderFinish(): bool
    {
        return $this->autoDeductStockOnServiceOrderFinish;
    }

    public function minimumStockDefault(): int
    {
        return $this->minimumStockDefault;
    }

    public function notifyMinimumStock(): bool
    {
        return $this->notifyMinimumStock;
    }

    public function notifyZeroStock(): bool
    {
        return $this->notifyZeroStock;
    }

    public function notificationEmail(): ?string
    {
        return $this->notificationEmail;
    }

    public function notificationPhone(): ?string
    {
        return $this->notificationPhone;
    }

    public function plan(): string
    {
        return $this->plan;
    }

    public function userLimit(): int
    {
        return $this->userLimit;
    }
}
