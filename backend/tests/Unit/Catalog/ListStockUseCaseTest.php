<?php

namespace Tests\Unit\Catalog;

use App\Modules\Catalog\Application\UseCases\ListStock\ListStockInput;
use App\Modules\Catalog\Application\UseCases\ListStock\ListStockOutput;
use App\Modules\Catalog\Application\UseCases\ListStock\ListStockUseCase;
use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Factories\ProductFactory;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Inventory\Domain\Entities\InventoryItem;
use App\Modules\Inventory\Domain\Repositories\InventoryItemRepository;
use App\Modules\Inventory\Domain\ValueObjects\InventoryItemId;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Inventory\Domain\ValueObjects\StockQuantity;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use PHPUnit\Framework\TestCase;

class ListStockUseCaseTest extends TestCase
{
    public function test_it_lists_stock_products_for_tenant(): void
    {
        $repository = new ListStockInMemoryRepository;
        $inventoryItems = new ListStockInventoryItemRepository;
        $repository->save($this->product(
            id: '018f95f2-0f08-7f85-9b31-2d833a1a2f41',
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            name: 'Filtro de oleo',
            sku: 'FO-001',
            minimumStock: 3,
        ));
        $inventoryItems->save(new InventoryItem(
            id: new InventoryItemId('018f95f2-0f08-7f85-9b31-2d833a1a2f45'),
            tenantId: new TenantId('018f95f2-0f08-7f85-9b31-2d833a1a2f42'),
            productId: new StockProductId('018f95f2-0f08-7f85-9b31-2d833a1a2f41'),
            currentStock: new StockQuantity(7),
        ));
        $repository->save($this->product(
            id: '018f95f2-0f08-7f85-9b31-2d833a1a2f44',
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f43',
            name: 'Pastilha de freio',
            sku: 'PF-001',
        ));

        $output = (new ListStockUseCase($repository, $inventoryItems))->execute(new ListStockInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
        ));

        $this->assertInstanceOf(ListStockOutput::class, $output);
        $this->assertSame(1, $output->total());
        $this->assertSame('Filtro de oleo', $output->items[0]->name);
        $this->assertSame('FO-001', $output->items[0]->sku);
        $this->assertSame(7, $output->items[0]->currentStock);
        $this->assertSame('available', $output->items[0]->stockStatus);
    }

    public function test_it_filters_products_by_term(): void
    {
        $repository = new ListStockInMemoryRepository;
        $repository->save($this->product(
            id: '018f95f2-0f08-7f85-9b31-2d833a1a2f41',
            name: 'Filtro de oleo',
            sku: 'FO-001',
            barcode: '7891234567890',
            category: 'Filtros',
            brand: 'Mann',
        ));
        $repository->save($this->product(
            id: '018f95f2-0f08-7f85-9b31-2d833a1a2f44',
            name: 'Pastilha de freio',
            sku: 'PF-001',
            barcode: '7891234567891',
            category: 'Freios',
            brand: 'Bosch',
        ));

        $output = (new ListStockUseCase($repository, new ListStockInventoryItemRepository))->execute(new ListStockInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            term: 'bosch',
        ));

        $this->assertSame(1, $output->total());
        $this->assertSame('Pastilha de freio', $output->items[0]->name);
    }

    private function product(
        string $id,
        string $tenantId = '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
        string $name = 'Filtro de oleo',
        string $sku = 'FO-001',
        ?string $barcode = '7891234567890',
        ?string $category = 'Filtros',
        ?string $brand = 'Mann',
        int $minimumStock = 2,
    ): Product {
        return (new ProductFactory)->create(
            id: new ProductId($id),
            tenantId: new TenantId($tenantId),
            name: $name,
            sku: new Sku($sku),
            barcode: new Barcode($barcode),
            category: $category,
            brand: $brand,
            supplier: 'Auto Pecas Central',
            minimumStock: $minimumStock,
            cost: new Money(2590),
        );
    }
}

final class ListStockInMemoryRepository implements ProductRepository
{
    /**
     * @var array<int, Product>
     */
    private array $products = [];

    public function search(TenantId $tenantId, ?string $term = null): array
    {
        return array_values(array_filter(
            $this->products,
            function (Product $product) use ($tenantId, $term): bool {
                if (! $product->tenantId()->equals($tenantId)) {
                    return false;
                }

                if ($term === null || trim($term) === '') {
                    return true;
                }

                $term = mb_strtolower(trim($term));

                return str_contains(mb_strtolower($product->name()), $term)
                    || str_contains(mb_strtolower($product->sku()->value), $term)
                    || ($product->barcode()->value !== null && str_contains(mb_strtolower($product->barcode()->value), $term))
                    || ($product->category() !== null && str_contains(mb_strtolower($product->category()), $term))
                    || ($product->brand() !== null && str_contains(mb_strtolower($product->brand()), $term));
            },
        ));
    }

    public function findById(TenantId $tenantId, ProductId $productId): ?Product
    {
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

final class ListStockInventoryItemRepository implements InventoryItemRepository
{
    /**
     * @var array<int, InventoryItem>
     */
    private array $items = [];

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
