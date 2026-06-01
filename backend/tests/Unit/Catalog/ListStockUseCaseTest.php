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
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use PHPUnit\Framework\TestCase;

class ListStockUseCaseTest extends TestCase
{
    public function test_it_lists_stock_products_for_tenant(): void
    {
        $repository = new ListStockInMemoryRepository;
        $repository->save($this->product(
            id: '018f95f2-0f08-7f85-9b31-2d833a1a2f41',
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            name: 'Filtro de oleo',
            sku: 'FO-001',
            minimumStock: 3,
        ));
        $repository->save($this->product(
            id: '018f95f2-0f08-7f85-9b31-2d833a1a2f44',
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f43',
            name: 'Pastilha de freio',
            sku: 'PF-001',
        ));

        $output = (new ListStockUseCase($repository))->execute(new ListStockInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
        ));

        $this->assertInstanceOf(ListStockOutput::class, $output);
        $this->assertSame(1, $output->total());
        $this->assertSame('Filtro de oleo', $output->items[0]->name);
        $this->assertSame('FO-001', $output->items[0]->sku);
        $this->assertSame(0, $output->items[0]->currentStock);
        $this->assertSame('zero', $output->items[0]->stockStatus);
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

        $output = (new ListStockUseCase($repository))->execute(new ListStockInput(
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
