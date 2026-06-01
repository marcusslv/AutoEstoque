<?php

namespace App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Factories\ProductFactory;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

final class EloquentProductRepository implements ProductRepository
{
    public function __construct(private readonly ProductFactory $productFactory) {}

    public function findById(TenantId $tenantId, ProductId $productId): ?Product
    {
        $model = ProductModel::query()
            ->where('tenant_id', $tenantId->value)
            ->where('id', $productId->value)
            ->first();

        if (! $model instanceof ProductModel) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function existsBySku(TenantId $tenantId, Sku $sku): bool
    {
        return ProductModel::query()
            ->where('tenant_id', $tenantId->value)
            ->where('sku', $sku->value)
            ->exists();
    }

    public function existsByBarcode(TenantId $tenantId, Barcode $barcode): bool
    {
        if ($barcode->value === null) {
            return false;
        }

        return ProductModel::query()
            ->where('tenant_id', $tenantId->value)
            ->where('barcode', $barcode->value)
            ->exists();
    }

    public function existsBySkuIgnoringProduct(TenantId $tenantId, Sku $sku, ProductId $productId): bool
    {
        return ProductModel::query()
            ->where('tenant_id', $tenantId->value)
            ->where('sku', $sku->value)
            ->where('id', '!=', $productId->value)
            ->exists();
    }

    public function existsByBarcodeIgnoringProduct(TenantId $tenantId, Barcode $barcode, ProductId $productId): bool
    {
        if ($barcode->value === null) {
            return false;
        }

        return ProductModel::query()
            ->where('tenant_id', $tenantId->value)
            ->where('barcode', $barcode->value)
            ->where('id', '!=', $productId->value)
            ->exists();
    }

    public function save(Product $product): void
    {
        ProductModel::query()->create([
            'id' => $product->id()->value,
            'tenant_id' => $product->tenantId()->value,
            'name' => $product->name(),
            'sku' => $product->sku()->value,
            'barcode' => $product->barcode()->value,
            'category' => $product->category(),
            'brand' => $product->brand(),
            'supplier' => $product->supplier(),
            'minimum_stock' => $product->minimumStock(),
            'cost_in_cents' => $product->cost()->amountInCents,
            'currency' => $product->cost()->currency,
        ]);
    }

    public function update(Product $product): void
    {
        ProductModel::query()
            ->where('tenant_id', $product->tenantId()->value)
            ->where('id', $product->id()->value)
            ->update([
                'name' => $product->name(),
                'sku' => $product->sku()->value,
                'barcode' => $product->barcode()->value,
                'category' => $product->category(),
                'brand' => $product->brand(),
                'supplier' => $product->supplier(),
                'minimum_stock' => $product->minimumStock(),
                'cost_in_cents' => $product->cost()->amountInCents,
                'currency' => $product->cost()->currency,
            ]);
    }

    private function toDomain(ProductModel $model): Product
    {
        return $this->productFactory->create(
            id: new ProductId((string) $model->id),
            tenantId: new TenantId((string) $model->tenant_id),
            name: (string) $model->name,
            sku: new Sku((string) $model->sku),
            barcode: new Barcode($model->barcode === null ? null : (string) $model->barcode),
            category: $model->category === null ? null : (string) $model->category,
            brand: $model->brand === null ? null : (string) $model->brand,
            supplier: $model->supplier === null ? null : (string) $model->supplier,
            minimumStock: (int) $model->minimum_stock,
            cost: new Money((int) $model->cost_in_cents, (string) $model->currency),
        );
    }
}
