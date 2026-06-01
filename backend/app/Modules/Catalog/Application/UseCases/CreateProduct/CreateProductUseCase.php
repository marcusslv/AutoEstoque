<?php

namespace App\Modules\Catalog\Application\UseCases\CreateProduct;

use App\Modules\Catalog\Domain\Exceptions\DuplicatedBarcodeException;
use App\Modules\Catalog\Domain\Exceptions\DuplicatedSkuException;
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
use Illuminate\Support\Str;

/**
 * @implements UseCase<CreateProductInput, CreateProductOutput>
 */
final readonly class CreateProductUseCase implements UseCase
{
    public function __construct(
        private ProductRepository $products,
        private ProductFactory $productFactory,
    ) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof CreateProductInput);

        $tenantId = new TenantId($input->tenantId);
        $sku = new Sku($input->sku);
        $barcode = new Barcode($input->barcode);

        if ($this->products->existsBySku($tenantId, $sku)) {
            throw new DuplicatedSkuException;
        }

        if ($barcode->value !== null && $this->products->existsByBarcode($tenantId, $barcode)) {
            throw new DuplicatedBarcodeException;
        }

        $product = $this->productFactory->create(
            id: new ProductId((string) Str::uuid()),
            tenantId: $tenantId,
            name: $input->name,
            sku: $sku,
            barcode: $barcode,
            category: $input->category,
            brand: $input->brand,
            supplier: $input->supplier,
            minimumStock: $input->minimumStock,
            cost: new Money($input->costInCents, $input->currency),
        );

        $this->products->save($product);

        return new CreateProductOutput(
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
