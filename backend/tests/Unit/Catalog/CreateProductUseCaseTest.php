<?php

namespace Tests\Unit\Catalog;

use App\Modules\Catalog\Application\UseCases\CreateProduct\CreateProductInput;
use App\Modules\Catalog\Application\UseCases\CreateProduct\CreateProductOutput;
use App\Modules\Catalog\Application\UseCases\CreateProduct\CreateProductUseCase;
use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Exceptions\DuplicatedBarcodeException;
use App\Modules\Catalog\Domain\Exceptions\DuplicatedSkuException;
use App\Modules\Catalog\Domain\Factories\ProductFactory;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use PHPUnit\Framework\TestCase;

class CreateProductUseCaseTest extends TestCase
{
    public function test_it_creates_a_product(): void
    {
        $repository = new InMemoryProductRepository;
        $useCase = new CreateProductUseCase($repository, new ProductFactory);

        $output = $useCase->execute($this->input());

        $this->assertInstanceOf(CreateProductOutput::class, $output);
        $this->assertSame('018f95f2-0f08-7f85-9b31-2d833a1a2f42', $output->tenantId);
        $this->assertSame('Filtro de oleo', $output->name);
        $this->assertSame('FO-001', $output->sku);
        $this->assertSame('7891234567890', $output->barcode);
        $this->assertSame(3, $output->minimumStock);
        $this->assertSame(2590, $output->costInCents);
        $this->assertSame('BRL', $output->currency);
        $this->assertCount(1, $repository->products);
    }

    public function test_it_rejects_duplicated_sku(): void
    {
        $repository = new InMemoryProductRepository;
        $useCase = new CreateProductUseCase($repository, new ProductFactory);

        $useCase->execute($this->input());

        $this->expectException(DuplicatedSkuException::class);

        $useCase->execute($this->input(barcode: '7891234567891'));
    }

    public function test_it_rejects_duplicated_barcode(): void
    {
        $repository = new InMemoryProductRepository;
        $useCase = new CreateProductUseCase($repository, new ProductFactory);

        $useCase->execute($this->input());

        $this->expectException(DuplicatedBarcodeException::class);

        $useCase->execute($this->input(sku: 'FA-001'));
    }

    public function test_it_allows_same_sku_in_different_tenants(): void
    {
        $repository = new InMemoryProductRepository;
        $useCase = new CreateProductUseCase($repository, new ProductFactory);

        $useCase->execute($this->input());
        $useCase->execute($this->input(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f43',
        ));

        $this->assertCount(2, $repository->products);
    }

    private function input(
        string $tenantId = '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
        string $name = 'Filtro de oleo',
        string $sku = 'FO-001',
        ?string $barcode = '7891234567890',
    ): CreateProductInput {
        return new CreateProductInput(
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
}

final class InMemoryProductRepository implements ProductRepository
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
