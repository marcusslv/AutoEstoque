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
use App\Modules\Inventory\Application\UseCases\RegisterStockEntry\RegisterStockEntryInput;
use App\Modules\Inventory\Application\UseCases\RegisterStockEntry\RegisterStockEntryOutput;
use App\Modules\Inventory\Application\UseCases\RegisterStockEntry\RegisterStockEntryUseCase;
use App\Modules\Inventory\Domain\Entities\InventoryItem;
use App\Modules\Inventory\Domain\Entities\StockMovement;
use App\Modules\Inventory\Domain\Factories\InventoryItemFactory;
use App\Modules\Inventory\Domain\Factories\StockMovementFactory;
use App\Modules\Inventory\Domain\Repositories\InventoryItemRepository;
use App\Modules\Inventory\Domain\Repositories\StockMovementRepository;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Shared\Application\Contracts\TransactionManager;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use PHPUnit\Framework\TestCase;

class RegisterStockEntryUseCaseTest extends TestCase
{
    public function test_it_registers_stock_entry_creating_inventory_item(): void
    {
        $products = new RegisterStockEntryProductRepository;
        $products->save($this->product());
        $inventoryItems = new RegisterStockEntryInventoryItemRepository;
        $stockMovements = new RegisterStockEntryMovementRepository;

        $output = $this->useCase($products, $inventoryItems, $stockMovements)
            ->execute($this->input(quantity: 5));

        $this->assertInstanceOf(RegisterStockEntryOutput::class, $output);
        $this->assertSame(5, $output->quantity);
        $this->assertSame(5, $output->currentStock);
        $this->assertSame('purchase', $output->type);
        $this->assertCount(1, $inventoryItems->items);
        $this->assertCount(1, $stockMovements->movements);
    }

    public function test_it_registers_stock_entry_increasing_existing_inventory_item(): void
    {
        $products = new RegisterStockEntryProductRepository;
        $products->save($this->product());
        $inventoryItems = new RegisterStockEntryInventoryItemRepository;
        $stockMovements = new RegisterStockEntryMovementRepository;
        $useCase = $this->useCase($products, $inventoryItems, $stockMovements);

        $useCase->execute($this->input(quantity: 5));
        $output = $useCase->execute($this->input(quantity: 3));

        $this->assertSame(8, $output->currentStock);
        $this->assertCount(1, $inventoryItems->items);
        $this->assertCount(2, $stockMovements->movements);
    }

    public function test_it_rejects_missing_product(): void
    {
        $this->expectException(ProductNotFoundException::class);

        $this->useCase(
            new RegisterStockEntryProductRepository,
            new RegisterStockEntryInventoryItemRepository,
            new RegisterStockEntryMovementRepository,
        )->execute($this->input());
    }

    private function useCase(
        ProductRepository $products,
        InventoryItemRepository $inventoryItems,
        StockMovementRepository $stockMovements,
    ): RegisterStockEntryUseCase {
        return new RegisterStockEntryUseCase(
            products: $products,
            inventoryItems: $inventoryItems,
            stockMovements: $stockMovements,
            inventoryItemFactory: new InventoryItemFactory,
            stockMovementFactory: new StockMovementFactory,
            transactionManager: new ImmediateTransactionManager,
        );
    }

    private function input(int $quantity = 5): RegisterStockEntryInput
    {
        return new RegisterStockEntryInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            userId: '018f95f2-0f08-7f85-9b31-2d833a1a2f43',
            productId: '018f95f2-0f08-7f85-9b31-2d833a1a2f41',
            type: 'purchase',
            quantity: $quantity,
            reason: 'Compra de reposicao',
            note: 'Nota 123',
            unitCostInCents: 2590,
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
}

final class ImmediateTransactionManager implements TransactionManager
{
    public function run(callable $callback): mixed
    {
        return $callback();
    }
}

final class RegisterStockEntryProductRepository implements ProductRepository
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

final class RegisterStockEntryInventoryItemRepository implements InventoryItemRepository
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

final class RegisterStockEntryMovementRepository implements StockMovementRepository
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
