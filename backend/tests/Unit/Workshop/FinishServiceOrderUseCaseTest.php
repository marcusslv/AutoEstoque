<?php

namespace Tests\Unit\Workshop;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Factories\ProductFactory;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Inventory\Application\UseCases\RegisterStockOutput\RegisterStockOutputUseCase;
use App\Modules\Inventory\Domain\Entities\InventoryItem;
use App\Modules\Inventory\Domain\Entities\StockMovement;
use App\Modules\Inventory\Domain\Exceptions\InsufficientStockException;
use App\Modules\Inventory\Domain\Factories\InventoryItemFactory;
use App\Modules\Inventory\Domain\Factories\StockMovementFactory;
use App\Modules\Inventory\Domain\Repositories\InventoryItemRepository;
use App\Modules\Inventory\Domain\Repositories\StockMovementRepository;
use App\Modules\Inventory\Domain\ValueObjects\InventoryItemId;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Inventory\Domain\ValueObjects\StockQuantity;
use App\Modules\Shared\Application\Contracts\TransactionManager;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Application\UseCases\FinishServiceOrder\Dtos\FinishServiceOrderInput;
use App\Modules\Workshop\Application\UseCases\FinishServiceOrder\Dtos\FinishServiceOrderOutput;
use App\Modules\Workshop\Application\UseCases\FinishServiceOrder\FinishServiceOrderUseCase;
use App\Modules\Workshop\Domain\Entities\ServiceOrder;
use App\Modules\Workshop\Domain\Entities\ServiceOrderItem;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderHasNoItemsException;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotFoundException;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotOpenException;
use App\Modules\Workshop\Domain\Factories\ServiceOrderFactory;
use App\Modules\Workshop\Domain\Factories\ServiceOrderItemFactory;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderItemRepository;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderRepository;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderItemId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderStatus;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class FinishServiceOrderUseCaseTest extends TestCase
{
    public function test_it_finishes_service_order_and_registers_stock_outputs(): void
    {
        $serviceOrders = new FinishServiceOrderRepository;
        $serviceOrders->save($this->serviceOrder());

        $serviceOrderItems = new FinishServiceOrderItemRepository;
        $serviceOrderItems->save($this->serviceOrderItem(quantity: 2));

        $inventoryItems = new FinishInventoryItemRepository;
        $inventoryItems->save($this->inventoryItem(currentStock: 5));

        $stockMovements = new FinishStockMovementRepository;

        $output = $this->useCase(
            serviceOrders: $serviceOrders,
            serviceOrderItems: $serviceOrderItems,
            inventoryItems: $inventoryItems,
            stockMovements: $stockMovements,
        )->execute($this->input());

        $this->assertInstanceOf(FinishServiceOrderOutput::class, $output);
        $this->assertSame('finished', $output->status);
        $this->assertCount(1, $output->movementIds);
        $this->assertSame('finished', $serviceOrders->serviceOrders[0]->status()->value);
        $this->assertSame(3, $inventoryItems->items[0]->currentStock()->value);
        $this->assertCount(1, $stockMovements->movements);
        $this->assertSame('service_consumption', $stockMovements->movements[0]->type()->value);
    }

    public function test_it_rejects_missing_service_order(): void
    {
        $this->expectException(ServiceOrderNotFoundException::class);

        $this->useCase()->execute($this->input());
    }

    public function test_it_rejects_finished_service_order(): void
    {
        $serviceOrders = new FinishServiceOrderRepository;
        $serviceOrders->save($this->serviceOrder(status: ServiceOrderStatus::FINISHED));

        $this->expectException(ServiceOrderNotOpenException::class);

        $this->useCase(serviceOrders: $serviceOrders)->execute($this->input());
    }

    public function test_it_rejects_service_order_without_items(): void
    {
        $serviceOrders = new FinishServiceOrderRepository;
        $serviceOrders->save($this->serviceOrder());

        $this->expectException(ServiceOrderHasNoItemsException::class);

        $this->useCase(serviceOrders: $serviceOrders)->execute($this->input());
    }

    public function test_it_rejects_insufficient_stock_and_keeps_service_order_open(): void
    {
        $serviceOrders = new FinishServiceOrderRepository;
        $serviceOrders->save($this->serviceOrder());

        $serviceOrderItems = new FinishServiceOrderItemRepository;
        $serviceOrderItems->save($this->serviceOrderItem(quantity: 2));

        $inventoryItems = new FinishInventoryItemRepository;
        $inventoryItems->save($this->inventoryItem(currentStock: 1));

        $this->expectException(InsufficientStockException::class);

        try {
            $this->useCase(
                serviceOrders: $serviceOrders,
                serviceOrderItems: $serviceOrderItems,
                inventoryItems: $inventoryItems,
            )->execute($this->input());
        } finally {
            $this->assertSame('open', $serviceOrders->serviceOrders[0]->status()->value);
        }
    }

    private function useCase(
        ?FinishServiceOrderRepository $serviceOrders = null,
        ?FinishServiceOrderItemRepository $serviceOrderItems = null,
        ?FinishInventoryItemRepository $inventoryItems = null,
        ?FinishStockMovementRepository $stockMovements = null,
    ): FinishServiceOrderUseCase {
        $inventoryItems ??= new FinishInventoryItemRepository;
        $stockMovements ??= new FinishStockMovementRepository;

        $registerStockOutput = new RegisterStockOutputUseCase(
            new FinishProductRepository([$this->product()]),
            $inventoryItems,
            $stockMovements,
            new StockMovementFactory,
            new FinishFakeTransactionManager,
        );

        return new FinishServiceOrderUseCase(
            $serviceOrders ?? new FinishServiceOrderRepository,
            $serviceOrderItems ?? new FinishServiceOrderItemRepository,
            $registerStockOutput,
            new FinishFakeTransactionManager,
        );
    }

    private function input(): FinishServiceOrderInput
    {
        return new FinishServiceOrderInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            serviceOrderId: '018f95f2-0f08-7f85-9b31-2d833a1a2f43',
            finishedByUserId: '018f95f2-0f08-7f85-9b31-2d833a1a2f45',
        );
    }

    private function serviceOrder(string $status = ServiceOrderStatus::OPEN): ServiceOrder
    {
        return (new ServiceOrderFactory)->create(
            id: new ServiceOrderId('018f95f2-0f08-7f85-9b31-2d833a1a2f43'),
            tenantId: new TenantId('018f95f2-0f08-7f85-9b31-2d833a1a2f42'),
            vehicleId: new VehicleId('018f95f2-0f08-7f85-9b31-2d833a1a2f46'),
            createdByUserId: '018f95f2-0f08-7f85-9b31-2d833a1a2f45',
            customerName: 'Joao Silva',
            servicesDescription: 'Troca de oleo',
            observations: null,
            status: new ServiceOrderStatus($status),
            openedAt: new DateTimeImmutable('2026-06-02 10:00:00'),
        );
    }

    private function serviceOrderItem(int $quantity): ServiceOrderItem
    {
        return (new ServiceOrderItemFactory)->create(
            id: new ServiceOrderItemId('018f95f2-0f08-7f85-9b31-2d833a1a2f48'),
            tenantId: new TenantId('018f95f2-0f08-7f85-9b31-2d833a1a2f42'),
            serviceOrderId: new ServiceOrderId('018f95f2-0f08-7f85-9b31-2d833a1a2f43'),
            productId: new ProductId('018f95f2-0f08-7f85-9b31-2d833a1a2f44'),
            addedByUserId: '018f95f2-0f08-7f85-9b31-2d833a1a2f45',
            quantity: $quantity,
        );
    }

    private function product(): Product
    {
        return (new ProductFactory)->create(
            id: new ProductId('018f95f2-0f08-7f85-9b31-2d833a1a2f44'),
            tenantId: new TenantId('018f95f2-0f08-7f85-9b31-2d833a1a2f42'),
            name: 'Filtro de oleo',
            sku: new Sku('FO-001'),
            barcode: new Barcode(null),
            category: 'Filtros',
            brand: 'Mann',
            supplier: 'Auto Pecas Central',
            minimumStock: 1,
            cost: new Money(2590),
        );
    }

    private function inventoryItem(int $currentStock): InventoryItem
    {
        return (new InventoryItemFactory)->create(
            id: new InventoryItemId('018f95f2-0f08-7f85-9b31-2d833a1a2f47'),
            tenantId: new TenantId('018f95f2-0f08-7f85-9b31-2d833a1a2f42'),
            productId: new StockProductId('018f95f2-0f08-7f85-9b31-2d833a1a2f44'),
            currentStock: new StockQuantity($currentStock),
        );
    }
}

final class FinishServiceOrderRepository implements ServiceOrderRepository
{
    /**
     * @var array<int, ServiceOrder>
     */
    public array $serviceOrders = [];

    public function findById(TenantId $tenantId, ServiceOrderId $serviceOrderId): ?ServiceOrder
    {
        foreach ($this->serviceOrders as $serviceOrder) {
            if ($serviceOrder->tenantId()->equals($tenantId) && $serviceOrder->id()->value === $serviceOrderId->value) {
                return $serviceOrder;
            }
        }

        return null;
    }

    public function save(ServiceOrder $serviceOrder): void
    {
        $this->serviceOrders[] = $serviceOrder;
    }

    public function update(ServiceOrder $serviceOrder): void
    {
        foreach ($this->serviceOrders as $index => $currentServiceOrder) {
            if (
                $currentServiceOrder->tenantId()->equals($serviceOrder->tenantId())
                && $currentServiceOrder->id()->value === $serviceOrder->id()->value
            ) {
                $this->serviceOrders[$index] = $serviceOrder;
            }
        }
    }
}

final class FinishServiceOrderItemRepository implements ServiceOrderItemRepository
{
    /**
     * @var array<int, ServiceOrderItem>
     */
    public array $items = [];

    public function listByServiceOrder(TenantId $tenantId, ServiceOrderId $serviceOrderId): array
    {
        return array_values(array_filter(
            $this->items,
            fn (ServiceOrderItem $item): bool => $item->tenantId()->equals($tenantId)
                && $item->serviceOrderId()->value === $serviceOrderId->value,
        ));
    }

    public function save(ServiceOrderItem $item): void
    {
        $this->items[] = $item;
    }
}

final class FinishInventoryItemRepository implements InventoryItemRepository
{
    /**
     * @var array<int, InventoryItem>
     */
    public array $items = [];

    public function findByProductId(TenantId $tenantId, StockProductId $productId): ?InventoryItem
    {
        foreach ($this->items as $item) {
            if ($item->tenantId()->equals($tenantId) && $item->productId()->value === $productId->value) {
                return $item;
            }
        }

        return null;
    }

    public function save(InventoryItem $item): void
    {
        $this->items[] = $item;
    }

    public function update(InventoryItem $item): void
    {
        foreach ($this->items as $index => $currentItem) {
            if ($currentItem->tenantId()->equals($item->tenantId()) && $currentItem->productId()->value === $item->productId()->value) {
                $this->items[$index] = $item;
            }
        }
    }
}

final class FinishStockMovementRepository implements StockMovementRepository
{
    /**
     * @var array<int, StockMovement>
     */
    public array $movements = [];

    public function save(StockMovement $movement): void
    {
        $this->movements[] = $movement;
    }
}

final class FinishProductRepository implements ProductRepository
{
    /**
     * @param  array<int, Product>  $products
     */
    public function __construct(private array $products = []) {}

    public function search(TenantId $tenantId, ?string $term = null): array
    {
        return [];
    }

    public function findById(TenantId $tenantId, ProductId $productId): ?Product
    {
        foreach ($this->products as $product) {
            if ($product->tenantId()->equals($tenantId) && $product->id()->value === $productId->value) {
                return $product;
            }
        }

        return null;
    }

    public function existsBySku(TenantId $tenantId, Sku $sku): bool
    {
        return false;
    }

    public function existsByBarcode(TenantId $tenantId, Barcode $barcode): bool
    {
        return false;
    }

    public function existsBySkuIgnoringProduct(TenantId $tenantId, Sku $sku, ProductId $productId): bool
    {
        return false;
    }

    public function existsByBarcodeIgnoringProduct(TenantId $tenantId, Barcode $barcode, ProductId $productId): bool
    {
        return false;
    }

    public function save(Product $product): void
    {
        $this->products[] = $product;
    }

    public function update(Product $product): void
    {
        //
    }
}

final class FinishFakeTransactionManager implements TransactionManager
{
    public function run(callable $callback): mixed
    {
        return $callback();
    }
}
