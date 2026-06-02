<?php

namespace App\Modules\Workshop\Application\UseCases\AddPartToServiceOrder;

use App\Modules\Catalog\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Inventory\Domain\Exceptions\InsufficientStockException;
use App\Modules\Inventory\Domain\Repositories\InventoryItemRepository;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Application\UseCases\AddPartToServiceOrder\Dtos\AddPartToServiceOrderInput;
use App\Modules\Workshop\Application\UseCases\AddPartToServiceOrder\Dtos\AddPartToServiceOrderOutput;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotFoundException;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotOpenException;
use App\Modules\Workshop\Domain\Factories\ServiceOrderItemFactory;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderItemRepository;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderRepository;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderItemId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderStatus;
use Illuminate\Support\Str;

/**
 * @implements UseCase<AddPartToServiceOrderInput, AddPartToServiceOrderOutput>
 */
final readonly class AddPartToServiceOrderUseCase implements UseCase
{
    public function __construct(
        private ServiceOrderRepository $serviceOrders,
        private ServiceOrderItemRepository $serviceOrderItems,
        private ProductRepository $products,
        private InventoryItemRepository $inventoryItems,
        private ServiceOrderItemFactory $serviceOrderItemFactory,
    ) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof AddPartToServiceOrderInput);

        $tenantId = new TenantId($input->tenantId);
        $serviceOrderId = new ServiceOrderId($input->serviceOrderId);
        $productId = new ProductId($input->productId);
        $stockProductId = new StockProductId($input->productId);

        $serviceOrder = $this->serviceOrders->findById($tenantId, $serviceOrderId);

        if ($serviceOrder === null) {
            throw new ServiceOrderNotFoundException;
        }

        if ($serviceOrder->status()->value !== ServiceOrderStatus::OPEN) {
            throw new ServiceOrderNotOpenException;
        }

        if ($this->products->findById($tenantId, $productId) === null) {
            throw new ProductNotFoundException;
        }

        $inventoryItem = $this->inventoryItems->findByProductId($tenantId, $stockProductId);

        if ($inventoryItem === null || $inventoryItem->currentStock()->value < $input->quantity) {
            throw new InsufficientStockException;
        }

        $item = $this->serviceOrderItemFactory->create(
            id: new ServiceOrderItemId((string) Str::uuid()),
            tenantId: $tenantId,
            serviceOrderId: $serviceOrderId,
            productId: $productId,
            addedByUserId: $input->addedByUserId,
            quantity: $input->quantity,
        );

        $this->serviceOrderItems->save($item);

        return new AddPartToServiceOrderOutput(
            id: $item->id()->value,
            tenantId: $item->tenantId()->value,
            serviceOrderId: $item->serviceOrderId()->value,
            productId: $item->productId()->value,
            addedByUserId: $item->addedByUserId(),
            quantity: $item->quantity(),
        );
    }
}
