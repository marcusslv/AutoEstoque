<?php

namespace Tests\Unit\Catalog;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Factories\ProductFactory;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Shared\Domain\Exceptions\DomainValidationException;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function test_it_creates_a_valid_product(): void
    {
        $product = $this->makeProduct();

        $this->assertSame('Filtro de oleo', $product->name());
        $this->assertSame('FO-001', $product->sku()->value);
        $this->assertSame('Filtros', $product->category());
        $this->assertSame(3, $product->minimumStock());
        $this->assertSame(2590, $product->cost()->amountInCents);
    }

    public function test_it_normalizes_blank_optional_text_fields_to_null(): void
    {
        $product = $this->makeProduct(
            category: ' ',
            brand: ' ',
            supplier: ' ',
        );

        $this->assertNull($product->category());
        $this->assertNull($product->brand());
        $this->assertNull($product->supplier());
    }

    public function test_it_rejects_product_without_name_using_domain_validator(): void
    {
        try {
            $this->makeProduct(name: '   ');
            $this->fail('Expected domain validation exception.');
        } catch (DomainValidationException $exception) {
            $this->assertSame([
                [
                    'field' => 'name',
                    'message' => 'Product name is required.',
                    'code' => 'product.name_required',
                ],
            ], $exception->errors());
        }
    }

    public function test_it_rejects_negative_minimum_stock_using_domain_validator(): void
    {
        try {
            $this->makeProduct(minimumStock: -1);
            $this->fail('Expected domain validation exception.');
        } catch (DomainValidationException $exception) {
            $this->assertSame([
                [
                    'field' => 'minimum_stock',
                    'message' => 'Minimum stock cannot be negative.',
                    'code' => 'product.minimum_stock_negative',
                ],
            ], $exception->errors());
        }
    }

    public function test_it_collects_multiple_domain_validation_errors(): void
    {
        try {
            $this->makeProduct(name: '   ', minimumStock: -1);
            $this->fail('Expected domain validation exception.');
        } catch (DomainValidationException $exception) {
            $this->assertSame([
                [
                    'field' => 'name',
                    'message' => 'Product name is required.',
                    'code' => 'product.name_required',
                ],
                [
                    'field' => 'minimum_stock',
                    'message' => 'Minimum stock cannot be negative.',
                    'code' => 'product.minimum_stock_negative',
                ],
            ], $exception->errors());
        }
    }

    private function makeProduct(
        string $name = 'Filtro de oleo',
        ?string $category = 'Filtros',
        ?string $brand = 'Mann',
        ?string $supplier = 'Auto Pecas Central',
        int $minimumStock = 3,
    ): Product {
        return (new ProductFactory)->create(
            id: new ProductId('018f95f2-0f08-7f85-9b31-2d833a1a2f41'),
            tenantId: new TenantId('018f95f2-0f08-7f85-9b31-2d833a1a2f42'),
            name: $name,
            sku: new Sku('FO-001'),
            barcode: new Barcode('7891234567890'),
            category: $category,
            brand: $brand,
            supplier: $supplier,
            minimumStock: $minimumStock,
            cost: new Money(2590),
        );
    }
}
