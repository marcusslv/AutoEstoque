<?php

namespace App\Modules\Workshop\Application\UseCases\FinishServiceOrder;

use App\Modules\Inventory\Application\UseCases\RegisterStockOutput\Dtos\RegisterStockOutputInput;
use App\Modules\Inventory\Application\UseCases\RegisterStockOutput\Dtos\RegisterStockOutputOutput;
use App\Modules\Inventory\Application\UseCases\RegisterStockOutput\RegisterStockOutputUseCase;
use App\Modules\Inventory\Domain\ValueObjects\StockMovementId;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\TransactionManager;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Application\UseCases\FinishServiceOrder\Dtos\FinishServiceOrderInput;
use App\Modules\Workshop\Application\UseCases\FinishServiceOrder\Dtos\FinishServiceOrderOutput;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderHasNoItemsException;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotFoundException;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotOpenException;
use App\Modules\Workshop\Domain\Factories\ServiceOrderStockMovementLinkFactory;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderItemRepository;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderRepository;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderStockMovementLinkRepository;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderStatus;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderStockMovementLinkId;
use DateTimeImmutable;
use Illuminate\Support\Str;

/**
 * @implements UseCase<FinishServiceOrderInput, FinishServiceOrderOutput>
 */
final readonly class FinishServiceOrderUseCase implements UseCase
{
    public function __construct(
        private ServiceOrderRepository $serviceOrders,
        private ServiceOrderItemRepository $serviceOrderItems,
        private RegisterStockOutputUseCase $registerStockOutput,
        private ServiceOrderStockMovementLinkRepository $serviceOrderStockMovementLinks,
        private ServiceOrderStockMovementLinkFactory $serviceOrderStockMovementLinkFactory,
        private TransactionManager $transactionManager,
    ) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof FinishServiceOrderInput);

        return $this->transactionManager->run(function () use ($input): FinishServiceOrderOutput {
            $tenantId = new TenantId($input->tenantId);
            $serviceOrderId = new ServiceOrderId($input->serviceOrderId);

            $serviceOrder = $this->serviceOrders->findById($tenantId, $serviceOrderId);

            if ($serviceOrder === null) {
                throw new ServiceOrderNotFoundException;
            }

            if ($serviceOrder->status()->value !== ServiceOrderStatus::OPEN) {
                throw new ServiceOrderNotOpenException;
            }

            $items = $this->serviceOrderItems->listByServiceOrder($tenantId, $serviceOrderId);

            if ($items === []) {
                throw new ServiceOrderHasNoItemsException;
            }

            $movementIds = [];

            foreach ($items as $item) {
                $movement = $this->registerStockOutput->execute(new RegisterStockOutputInput(
                    tenantId: $tenantId->value,
                    userId: $input->finishedByUserId,
                    productId: $item->productId()->value,
                    type: 'service_consumption',
                    quantity: $item->quantity(),
                    reason: 'Consumo em ordem de servico',
                    note: "Ordem de servico {$serviceOrderId->value}",
                ));

                assert($movement instanceof RegisterStockOutputOutput);

                $this->serviceOrderStockMovementLinks->save($this->serviceOrderStockMovementLinkFactory->create(
                    id: new ServiceOrderStockMovementLinkId((string) Str::uuid()),
                    tenantId: $tenantId,
                    serviceOrderId: $serviceOrderId,
                    serviceOrderItemId: $item->id(),
                    stockMovementId: new StockMovementId($movement->movementId),
                ));

                $movementIds[] = $movement->movementId;
            }

            $serviceOrder->finish(new DateTimeImmutable);
            $this->serviceOrders->update($serviceOrder);

            return new FinishServiceOrderOutput(
                id: $serviceOrder->id()->value,
                tenantId: $serviceOrder->tenantId()->value,
                status: $serviceOrder->status()->value,
                finishedAt: $serviceOrder->finishedAt()?->format(DATE_ATOM) ?? '',
                movementIds: $movementIds,
            );
        });
    }
}
