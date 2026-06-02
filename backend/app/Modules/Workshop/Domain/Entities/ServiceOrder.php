<?php

namespace App\Modules\Workshop\Domain\Entities;

use App\Modules\Shared\Domain\Entities\Entity;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Validators\ServiceOrderValidator;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderStatus;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use DateTimeImmutable;

final class ServiceOrder extends Entity
{
    public function __construct(
        private readonly ServiceOrderId $id,
        private readonly TenantId $tenantId,
        private readonly VehicleId $vehicleId,
        private readonly string $createdByUserId,
        private readonly string $customerName,
        private readonly string $servicesDescription,
        private readonly ?string $observations,
        private ServiceOrderStatus $status,
        private readonly DateTimeImmutable $openedAt,
        private ?DateTimeImmutable $finishedAt,
    ) {
        parent::__construct();

        ServiceOrderValidator::validate($this);

        $this->throwIfNotificationHasErrors();
    }

    public function id(): ServiceOrderId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function vehicleId(): VehicleId
    {
        return $this->vehicleId;
    }

    public function createdByUserId(): string
    {
        return $this->createdByUserId;
    }

    public function customerName(): string
    {
        return $this->customerName;
    }

    public function servicesDescription(): string
    {
        return $this->servicesDescription;
    }

    public function observations(): ?string
    {
        return $this->observations;
    }

    public function status(): ServiceOrderStatus
    {
        return $this->status;
    }

    public function openedAt(): DateTimeImmutable
    {
        return $this->openedAt;
    }

    public function finishedAt(): ?DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function finish(DateTimeImmutable $finishedAt): void
    {
        if ($this->status->value !== ServiceOrderStatus::OPEN) {
            return;
        }

        $this->status = new ServiceOrderStatus(ServiceOrderStatus::FINISHED);
        $this->finishedAt = $finishedAt;
    }
}
