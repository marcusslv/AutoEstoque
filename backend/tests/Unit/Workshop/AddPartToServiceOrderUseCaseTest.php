<?php

namespace Tests\Unit\Workshop;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Catalog\Domain\Factories\ProductFactory;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Inventory\Domain\Entities\InventoryItem;
use App\Modules\Inventory\Domain\Exceptions\InsufficientStockException;
use App\Modules\Inventory\Domain\Factories\InventoryItemFactory;
use App\Modules\Inventory\Domain\Repositories\InventoryItemRepository;
use App\Modules\Inventory\Domain\ValueObjects\InventoryItemId;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Inventory\Domain\ValueObjects\StockQuantity;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use App\Modules\Workshop\Application\UseCases\AddPartToServiceOrder\AddPartToServiceOrderUseCase;
use App\Modules\Workshop\Application\UseCases\AddPartToServiceOrder\Dtos\AddPartToServiceOrderInput;
use App\Modules\Workshop\Application\UseCases\AddPartToServiceOrder\Dtos\AddPartToServiceOrderOutput;
use App\Modules\Workshop\Domain\Entities\ServiceOrder;
use App\Modules\Workshop\Domain\Entities\ServiceOrderItem;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotFoundException;
use App\Modules\Workshop\Domain\Exceptions\ServiceOrderNotOpenException;
use App\Modules\Workshop\Domain\Factories\ServiceOrderFactory;
use App\Modules\Workshop\Domain\Factories\ServiceOrderItemFactory;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderItemRepository;
use App\Modules\Workshop\Domain\Repositories\ServiceOrderRepository;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderId;
use App\Modules\Workshop\Domain\ValueObjects\ServiceOrderStatus;
use App\Modules\Workshop\Domain\ValueObjects\VehicleId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class AddPartToServiceOrderUseCaseTest extends TestCase
{
    public function test_it_adds_a_part_to_an_open_service_order(): void
    {
        $serviceOrders = new AddPartServiceOrderRepository;
        $serviceOrders->save($this->serviceOrder());

        $serviceOrderItems = new AddPartServiceOrderItemRepository;
        $inventoryItems = new AddPartInventoryItemRepository;
        $inventoryItems->save($this->inventoryItem(currentStock: 5));

        $useCase = new AddPartToServiceOrderUseCase(
            $serviceOrders,
            $serviceOrderItems,
            new AddPartProductRepository([$this->product()]),
            $inventoryItems,
            new ServiceOrderItemFactory,
        );

        $output = $useCase->execute($this->input(quantity: 2));

        $this->assertInstanceOf(AddPartToServiceOrderOutput::class, $output);
        $this->assertSame('018f95f2-0f08-7f85-9b31-2d833a1a2f43', $output->serviceOrderId);
        $this->assertSame('018f95f2-0f08-7f85-9b31-2d833a1a2f44', $output->productId);
        $this->assertSame(2, $output->quantity);
        $this->assertCount(1, $serviceOrderItems->items);
        $this->assertSame(5, $inventoryItems->items[0]->currentStock()->value);
    }

    public function test_it_rejects_missing_service_order(): void
    {
        $this->expectException(ServiceOrderNotFoundException::class);

        $this->useCase()->execute($this->input());
    }

    public function test_it_rejects_finished_service_order(): void
    {
        $serviceOrders = new AddPartServiceOrderRepository;
        $serviceOrders->save($this->serviceOrder(status: ServiceOrderStatus::FINISHED));

        $this->expectException(ServiceOrderNotOpenException::class);

        $this->useCase(serviceOrders: $serviceOrders)->execute($this->input());
    }

    public function test_it_rejects_missing_product(): void
    {
        $serviceOrders = new AddPartServiceOrderRepository;
        $serviceOrders->save($this->serviceOrder());

        $this->expectException(ProductNotFoundException::class);

        $this->useCase(
            serviceOrders: $serviceOrders,
            products: new AddPartProductRepository,
        )->execute($this->input());
    }

    public function test_it_rejects_insufficient_stock(): void
    {
        $serviceOrders = new AddPartServiceOrderRepository;
        $serviceOrders->save($this->serviceOrder());

        $inventoryItems = new AddPartInventoryItemRepository;
        $inventoryItems->save($this->inventoryItem(currentStock: 1));

        $this->expectException(InsufficientStockException::class);

        $this->useCase(
            serviceOrders: $serviceOrders,
            inventoryItems: $inventoryItems,
        )->execute($this->input(quantity: 2));
    }

    private function useCase(
        ?AddPartServiceOrderRepository $serviceOrders = null,
        ?AddPartProductRepository $products = null,
        ?AddPartInventoryItemRepository $inventoryItems = null,
    ): AddPartToServiceOrderUseCase {
        $inventoryItems ??= new AddPartInventoryItemRepository;
        $inventoryItems->save($this->inventoryItem(currentStock: 5));

        return new AddPartToServiceOrderUseCase(
            $serviceOrders ?? new AddPartServiceOrderRepository,
            new AddPartServiceOrderItemRepository,
            $products ?? new AddPartProductRepository([$this->product()]),
            $inventoryItems,
            new ServiceOrderItemFactory,
        );
    }

    private function input(int $quantity = 1): AddPartToServiceOrderInput
    {
        return new AddPartToServiceOrderInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            serviceOrderId: '018f95f2-0f08-7f85-9b31-2d833a1a2f43',
            productId: '018f95f2-0f08-7f85-9b31-2d833a1a2f44',
            addedByUserId: '018f95f2-0f08-7f85-9b31-2d833a1a2f45',
            quantity: $quantity,
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

final class AddPartServiceOrderRepository implements ServiceOrderRepository
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
}

final class AddPartServiceOrderItemRepository implements ServiceOrderItemRepository
{
    /**
     * @var array<int, ServiceOrderItem>
     */
    public array $items = [];

    public function save(ServiceOrderItem $item): void
    {
        $this->items[] = $item;
    }
}

final class AddPartProductRepository implements ProductRepository
{
    /**
     * @param  array<int, Product>  $products
     */
    public function __construct(public array $products = []) {}

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

final class AddPartInventoryItemRepository implements InventoryItemRepository
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
        //
    }
}
