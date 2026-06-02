<?php

namespace App\Modules\Workshop\Application\UseCases\CreateServiceOrder;

use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Application\UseCases\CreateServiceOrder\Dtos\CreateServiceOrderInput;
use App\Modules\Workshop\Application\UseCases\CreateServiceOrder\Dtos\CreateServiceOrderOutput;
use App\Modules\Workshop\Domain\Exceptions\VehicleNotFoundException;
use App\Modules\Workshop\Domain\Factories\ServiceOrderFactory;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderRepository;
use App\Modules\Workshop\Domain\Repositories\VehicleRepository;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderStatus;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use DateTimeImmutable;
use Illuminate\Support\Str;

/**
 * @implements UseCase<CreateServiceOrderInput, CreateServiceOrderOutput>
 */
final readonly class CreateServiceOrderUseCase implements UseCase
{
    public function __construct(
        private VehicleRepository $vehicles,
        private ServiceOrderRepository $serviceOrders,
        private ServiceOrderFactory $serviceOrderFactory,
    ) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof CreateServiceOrderInput);

        $tenantId = new TenantId($input->tenantId);
        $vehicleId = new VehicleId($input->vehicleId);

        if ($this->vehicles->findById($tenantId, $vehicleId) === null) {
            throw new VehicleNotFoundException;
        }

        $serviceOrder = $this->serviceOrderFactory->create(
            id: new ServiceOrderId((string) Str::uuid()),
            tenantId: $tenantId,
            vehicleId: $vehicleId,
            createdByUserId: $input->createdByUserId,
            customerName: $input->customerName,
            servicesDescription: $input->servicesDescription,
            observations: $input->observations,
            status: new ServiceOrderStatus(ServiceOrderStatus::OPEN),
            openedAt: new DateTimeImmutable,
        );

        $this->serviceOrders->save($serviceOrder);

        return new CreateServiceOrderOutput(
            id: $serviceOrder->id()->value,
            tenantId: $serviceOrder->tenantId()->value,
            vehicleId: $serviceOrder->vehicleId()->value,
            createdByUserId: $serviceOrder->createdByUserId(),
            customerName: $serviceOrder->customerName(),
            servicesDescription: $serviceOrder->servicesDescription(),
            observations: $serviceOrder->observations(),
            status: $serviceOrder->status()->value,
            openedAt: $serviceOrder->openedAt()->format(DATE_ATOM),
        );
    }
}
