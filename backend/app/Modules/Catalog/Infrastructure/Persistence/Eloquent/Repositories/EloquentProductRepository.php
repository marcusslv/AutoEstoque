<?php

namespace App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

final class EloquentProductRepository implements ProductRepository
{
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
}
