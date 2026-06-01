<?php

namespace Tests\Unit\Inventory;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Catalog\Domain\Factories\ProductFactory;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Inventory\Application\UseCases\RegisterStockAdjustment\RegisterStockAdjustmentInput;
use App\Modules\Inventory\Application\UseCases\RegisterStockAdjustment\RegisterStockAdjustmentOutput;
use App\Modules\Inventory\Application\UseCases\RegisterStockAdjustment\RegisterStockAdjustmentUseCase;
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
use PHPUnit\Framework\TestCase;

class RegisterStockAdjustmentUseCaseTest extends TestCase
{
    public function test_it_registers_entry_adjustment_creating_inventory_item(): void
    {
        $products = new RegisterStockAdjustmentProductRepository;
        $products->save($this->product());
        $inventoryItems = new RegisterStockAdjustmentInventoryItemRepository;
        $stockMovements = new RegisterStockAdjustmentMovementRepository;

        $output = $this->useCase($products, $inventoryItems, $stockMovements)
            ->execute($this->input(direction: 'entry', quantity: 4));

        $this->assertInstanceOf(RegisterStockAdjustmentOutput::class, $output);
        $this->assertSame('entry', $output->direction);
        $this->assertSame('manual_adjustment', $output->type);
        $this->assertSame(4, $output->currentStock);
        $this->assertCount(1, $inventoryItems->items);
        $this->assertCount(1, $stockMovements->movements);
    }

    public function test_it_registers_output_adjustment_decreasing_inventory_item(): void
    {
        $products = new RegisterStockAdjustmentProductRepository;
        $products->save($this->product());
        $inventoryItems = new RegisterStockAdjustmentInventoryItemRepository;
        $inventoryItems->save($this->inventoryItem(currentStock: 5));
        $stockMovements = new RegisterStockAdjustmentMovementRepository;

        $output = $this->useCase($products, $inventoryItems, $stockMovements)
            ->execute($this->input(direction: 'output', quantity: 2));

        $this->assertSame('output', $output->direction);
        $this->assertSame('manual_adjustment', $output->type);
        $this->assertSame(3, $output->currentStock);
        $this->assertSame(3, $inventoryItems->items[0]->currentStock()->value);
        $this->assertCount(1, $stockMovements->movements);
    }

    public function test_it_rejects_output_adjustment_when_stock_is_insufficient(): void
    {
        $products = new RegisterStockAdjustmentProductRepository;
        $products->save($this->product());
        $inventoryItems = new RegisterStockAdjustmentInventoryItemRepository;
        $inventoryItems->save($this->inventoryItem(currentStock: 1));

        $this->expectException(InsufficientStockException::class);

        $this->useCase(
            $products,
            $inventoryItems,
            new RegisterStockAdjustmentMovementRepository,
        )->execute($this->input(direction: 'output', quantity: 2));
    }

    public function test_it_rejects_missing_product(): void
    {
        $this->expectException(ProductNotFoundException::class);

        $this->useCase(
            new RegisterStockAdjustmentProductRepository,
            new RegisterStockAdjustmentInventoryItemRepository,
            new RegisterStockAdjustmentMovementRepository,
        )->execute($this->input());
    }

    private function useCase(
        ProductRepository $products,
        InventoryItemRepository $inventoryItems,
        StockMovementRepository $stockMovements,
    ): RegisterStockAdjustmentUseCase {
        return new RegisterStockAdjustmentUseCase(
            products: $products,
            inventoryItems: $inventoryItems,
            stockMovements: $stockMovements,
            inventoryItemFactory: new InventoryItemFactory,
            stockMovementFactory: new StockMovementFactory,
            transactionManager: new RegisterStockAdjustmentImmediateTransactionManager,
        );
    }

    private function input(string $direction = 'entry', int $quantity = 4): RegisterStockAdjustmentInput
    {
        return new RegisterStockAdjustmentInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            userId: '018f95f2-0f08-7f85-9b31-2d833a1a2f43',
            productId: '018f95f2-0f08-7f85-9b31-2d833a1a2f41',
            direction: $direction,
            quantity: $quantity,
            reason: 'Conferencia de estoque',
            note: 'Inventario semanal',
        );
    }

    private function product(): Product
    {
        return (new ProductFactory)->create(
            id: new ProductId('018f95f2-0f08-7f85-9b31-2d833a1a2f41'),
            tenantId: new TenantId('018f95f2-0f08-7f85-9b31-2d833a1a2f42'),
            name: 'Filtro de oleo',
            sku: new Sku('FO-001'),
            barcode: new Barcode('7891234567890'),
            category: 'Filtros',
            brand: 'Mann',
            supplier: 'Auto Pecas Central',
            minimumStock: 2,
            cost: new Money(2590),
        );
    }

    private function inventoryItem(int $currentStock): InventoryItem
    {
        return (new InventoryItemFactory)->create(
            id: new InventoryItemId('018f95f2-0f08-7f85-9b31-2d833a1a2f44'),
            tenantId: new TenantId('018f95f2-0f08-7f85-9b31-2d833a1a2f42'),
            productId: new StockProductId('018f95f2-0f08-7f85-9b31-2d833a1a2f41'),
            currentStock: new StockQuantity($currentStock),
        );
    }
}

final class RegisterStockAdjustmentImmediateTransactionManager implements TransactionManager
{
    public function run(callable $callback): mixed
    {
        return $callback();
    }
}

final class RegisterStockAdjustmentProductRepository implements ProductRepository
{
    /**
     * @var array<int, Product>
     */
    private array $products = [];

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

final class RegisterStockAdjustmentInventoryItemRepository implements InventoryItemRepository
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
            if ($currentItem->id()->value === $item->id()->value) {
                $this->items[$index] = $item;

                return;
            }
        }
    }
}

final class RegisterStockAdjustmentMovementRepository implements StockMovementRepository
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
