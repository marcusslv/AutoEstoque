<?php

namespace Tests\Unit\Catalog;

use App\Modules\Catalog\Application\UseCases\UpdateProduct\UpdateProductInput;
use App\Modules\Catalog\Application\UseCases\UpdateProduct\UpdateProductOutput;
use App\Modules\Catalog\Application\UseCases\UpdateProduct\UpdateProductUseCase;
use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Exceptions\DuplicatedBarcodeException;
use App\Modules\Catalog\Domain\Exceptions\DuplicatedSkuException;
use App\Modules\Catalog\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Catalog\Domain\Factories\ProductFactory;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use PHPUnit\Framework\TestCase;

class UpdateProductUseCaseTest extends TestCase
{
    public function test_it_updates_a_product(): void
    {
        $repository = new UpdateProductInMemoryRepository;
        $repository->save($this->product(id: '018f95f2-0f08-7f85-9b31-2d833a1a2f41'));

        $useCase = new UpdateProductUseCase($repository, new ProductFactory);

        $output = $useCase->execute($this->input(
            productId: '018f95f2-0f08-7f85-9b31-2d833a1a2f41',
            name: 'Filtro de oleo atualizado',
            sku: 'FO-002',
            barcode: '7891234567899',
        ));

        $this->assertInstanceOf(UpdateProductOutput::class, $output);
        $this->assertSame('018f95f2-0f08-7f85-9b31-2d833a1a2f41', $output->id);
        $this->assertSame('Filtro de oleo atualizado', $output->name);
        $this->assertSame('FO-002', $output->sku);
        $this->assertSame('7891234567899', $output->barcode);
        $this->assertCount(1, $repository->products);
        $this->assertSame('FO-002', $repository->products[0]->sku()->value);
    }

    public function test_it_rejects_missing_product(): void
    {
        $useCase = new UpdateProductUseCase(new UpdateProductInMemoryRepository, new ProductFactory);

        $this->expectException(ProductNotFoundException::class);

        $useCase->execute($this->input(productId: '018f95f2-0f08-7f85-9b31-2d833a1a2f41'));
    }

    public function test_it_rejects_duplicated_sku(): void
    {
        $repository = new UpdateProductInMemoryRepository;
        $repository->save($this->product(id: '018f95f2-0f08-7f85-9b31-2d833a1a2f41', sku: 'FO-001'));
        $repository->save($this->product(id: '018f95f2-0f08-7f85-9b31-2d833a1a2f44', sku: 'FA-001', barcode: '7891234567891'));

        $useCase = new UpdateProductUseCase($repository, new ProductFactory);

        $this->expectException(DuplicatedSkuException::class);

        $useCase->execute($this->input(
            productId: '018f95f2-0f08-7f85-9b31-2d833a1a2f41',
            sku: 'FA-001',
            barcode: '7891234567892',
        ));
    }

    public function test_it_rejects_duplicated_barcode(): void
    {
        $repository = new UpdateProductInMemoryRepository;
        $repository->save($this->product(id: '018f95f2-0f08-7f85-9b31-2d833a1a2f41', barcode: '7891234567890'));
        $repository->save($this->product(id: '018f95f2-0f08-7f85-9b31-2d833a1a2f44', sku: 'FA-001', barcode: '7891234567891'));

        $useCase = new UpdateProductUseCase($repository, new ProductFactory);

        $this->expectException(DuplicatedBarcodeException::class);

        $useCase->execute($this->input(
            productId: '018f95f2-0f08-7f85-9b31-2d833a1a2f41',
            sku: 'FO-002',
            barcode: '7891234567891',
        ));
    }

    private function input(
        string $productId,
        string $tenantId = '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
        string $name = 'Filtro de oleo',
        string $sku = 'FO-001',
        ?string $barcode = '7891234567890',
    ): UpdateProductInput {
        return new UpdateProductInput(
            productId: $productId,
            tenantId: $tenantId,
            name: $name,
            sku: $sku,
            barcode: $barcode,
            category: 'Filtros',
            brand: 'Mann',
            supplier: 'Auto Pecas Central',
            minimumStock: 3,
            costInCents: 2590,
        );
    }

    private function product(
        string $id,
        string $sku = 'FO-001',
        ?string $barcode = '7891234567890',
    ): Product {
        return (new ProductFactory)->create(
            id: new ProductId($id),
            tenantId: new TenantId('018f95f2-0f08-7f85-9b31-2d833a1a2f42'),
            name: 'Filtro de oleo',
            sku: new Sku($sku),
            barcode: new Barcode($barcode),
            category: 'Filtros',
            brand: 'Mann',
            supplier: 'Auto Pecas Central',
            minimumStock: 3,
            cost: new Money(2590),
        );
    }
}

final class UpdateProductInMemoryRepository implements ProductRepository
{
    /**
     * @var array<int, Product>
     */
    public array $products = [];

    public function search(TenantId $tenantId, ?string $term = null): array
    {
        return array_values(array_filter(
            $this->products,
            fn (Product $product): bool => $product->tenantId()->equals($tenantId),
        ));
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
        foreach ($this->products as $product) {
            if ($product->tenantId()->equals($tenantId) && $product->sku()->value === $sku->value) {
                return true;
            }
        }

        return false;
    }

    public function existsByBarcode(TenantId $tenantId, Barcode $barcode): bool
    {
        foreach ($this->products as $product) {
            if (
                $product->tenantId()->equals($tenantId)
                && $product->barcode()->value !== null
                && $product->barcode()->value === $barcode->value
            ) {
                return true;
            }
        }

        return false;
    }

    public function existsBySkuIgnoringProduct(TenantId $tenantId, Sku $sku, ProductId $productId): bool
    {
        foreach ($this->products as $product) {
            if (
                $product->tenantId()->equals($tenantId)
                && $product->id()->value !== $productId->value
                && $product->sku()->value === $sku->value
            ) {
                return true;
            }
        }

        return false;
    }

    public function existsByBarcodeIgnoringProduct(TenantId $tenantId, Barcode $barcode, ProductId $productId): bool
    {
        foreach ($this->products as $product) {
            if (
                $product->tenantId()->equals($tenantId)
                && $product->id()->value !== $productId->value
                && $product->barcode()->value !== null
                && $product->barcode()->value === $barcode->value
            ) {
                return true;
            }
        }

        return false;
    }

    public function save(Product $product): void
    {
        $this->products[] = $product;
    }

    public function update(Product $product): void
    {
        foreach ($this->products as $index => $currentProduct) {
            if (
                $currentProduct->tenantId()->equals($product->tenantId())
                && $currentProduct->id()->value === $product->id()->value
            ) {
                $this->products[$index] = $product;

                return;
            }
        }
    }
}
