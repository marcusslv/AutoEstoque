<?php

namespace Tests\Unit\Workshop;

use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Domain\Entities\ServiceOrder;
use App\Modules\Workshop\Domain\Factories\ServiceOrderFactory;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderStatus;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ServiceOrderTest extends TestCase
{
    public function test_it_creates_a_valid_service_order(): void
    {
        $serviceOrder = $this->makeServiceOrder(observations: '  Cliente aguardando  ');

        $this->assertSame('Joao Silva', $serviceOrder->customerName());
        $this->assertSame('Troca de oleo e filtros', $serviceOrder->servicesDescription());
        $this->assertSame('Cliente aguardando', $serviceOrder->observations());
        $this->assertSame(ServiceOrderStatus::OPEN, $serviceOrder->status()->value);
        $this->assertSame('2026-06-02T10:00:00+00:00', $serviceOrder->openedAt()->format(DATE_ATOM));
    }

    public function test_it_normalizes_blank_observations_to_null(): void
    {
        $serviceOrder = $this->makeServiceOrder(observations: '   ');

        $this->assertNull($serviceOrder->observations());
    }

    public function test_it_finishes_an_open_service_order(): void
    {
        $serviceOrder = $this->makeServiceOrder();

        $serviceOrder->finish(new DateTimeImmutable('2026-06-02 11:00:00'));

        $this->assertSame(ServiceOrderStatus::FINISHED, $serviceOrder->status()->value);
        $this->assertSame('2026-06-02T11:00:00+00:00', $serviceOrder->finishedAt()?->format(DATE_ATOM));
    }

    public function test_it_rejects_blank_required_fields_using_domain_validator(): void
    {
        try {
            $this->makeServiceOrder(
                createdByUserId: ' ',
                customerName: ' ',
                servicesDescription: ' ',
            );
            $this->fail('Expected domain validation exception.');
        } catch (DomainValidationException $exception) {
            $this->assertSame([
                [
                    'field' => 'created_by_user_id',
                    'message' => 'Created by user id is required.',
                    'code' => 'service_order.created_by_user_required',
                ],
                [
                    'field' => 'customer_name',
                    'message' => 'Customer name is required.',
                    'code' => 'service_order.customer_name_required',
                ],
                [
                    'field' => 'services_description',
                    'message' => 'Services description is required.',
                    'code' => 'service_order.services_description_required',
                ],
            ], $exception->errors());
        }
    }

    private function makeServiceOrder(
        string $createdByUserId = '018f95f2-0f08-7f85-9b31-2d833a1a2f44',
        string $customerName = 'Joao Silva',
        string $servicesDescription = 'Troca de oleo e filtros',
        ?string $observations = null,
    ): ServiceOrder {
        return (new ServiceOrderFactory)->create(
            id: new ServiceOrderId('018f95f2-0f08-7f85-9b31-2d833a1a2f41'),
            tenantId: new TenantId('018f95f2-0f08-7f85-9b31-2d833a1a2f42'),
            vehicleId: new VehicleId('018f95f2-0f08-7f85-9b31-2d833a1a2f43'),
            createdByUserId: $createdByUserId,
            customerName: $customerName,
            servicesDescription: $servicesDescription,
            observations: $observations,
            status: new ServiceOrderStatus(ServiceOrderStatus::OPEN),
            openedAt: new DateTimeImmutable('2026-06-02 10:00:00'),
        );
    }
}
