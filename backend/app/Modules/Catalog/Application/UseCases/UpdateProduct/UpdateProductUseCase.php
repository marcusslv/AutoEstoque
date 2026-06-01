<?php

namespace App\Modules\Catalog\Application\UseCases\UpdateProduct;

use App\Modules\Catalog\Application\UseCases\UpdateProduct\Dtos\UpdateProductInput;
use App\Modules\Catalog\Application\UseCases\UpdateProduct\Dtos\UpdateProductOutput;
use App\Modules\Catalog\Domain\Exceptions\DuplicatedBarcodeException;
use App\Modules\Catalog\Domain\Exceptions\DuplicatedSkuException;
use App\Modules\Catalog\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Catalog\Domain\Factories\ProductFactory;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

/**
 * @implements UseCase<UpdateProductInput, UpdateProductOutput>
 */
final readonly class UpdateProductUseCase implements UseCase
{
    public function __construct(
        private ProductRepository $products,
        private ProductFactory $productFactory,
    ) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof UpdateProductInput);

        $tenantId = new TenantId($input->tenantId);
        $productId = new ProductId($input->productId);
        $sku = new Sku($input->sku);
        $barcode = new Barcode($input->barcode);

        $currentProduct = $this->products->findById($tenantId, $productId);

        if ($currentProduct === null) {
            throw new ProductNotFoundException;
        }

        if ($this->products->existsBySkuIgnoringProduct($tenantId, $sku, $productId)) {
            throw new DuplicatedSkuException;
        }

        if ($barcode->value !== null && $this->products->existsByBarcodeIgnoringProduct($tenantId, $barcode, $productId)) {
            throw new DuplicatedBarcodeException;
        }

        $product = $this->productFactory->create(
            id: $currentProduct->id(),
            tenantId: $currentProduct->tenantId(),
            name: $input->name,
            sku: $sku,
            barcode: $barcode,
            category: $input->category,
            brand: $input->brand,
            supplier: $input->supplier,
            minimumStock: $input->minimumStock,
            cost: new Money($input->costInCents, $input->currency),
        );

        $this->products->update($product);

        return new UpdateProductOutput(
            id: $product->id()->value,
            tenantId: $product->tenantId()->value,
            name: $product->name(),
            sku: $product->sku()->value,
            barcode: $product->barcode()->value,
            category: $product->category(),
            brand: $product->brand(),
            supplier: $product->supplier(),
            minimumStock: $product->minimumStock(),
            costInCents: $product->cost()->amountInCents,
            currency: $product->cost()->currency,
        );
    }
}
