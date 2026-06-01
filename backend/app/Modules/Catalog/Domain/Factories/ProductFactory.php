<?php

namespace App\Modules\Catalog\Domain\Factories;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Money;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

final class ProductFactory
{
    public function create(
        ProductId $id,
        TenantId $tenantId,
        string $name,
        Sku $sku,
        Barcode $barcode,
        ?string $category,
        ?string $brand,
        ?string $supplier,
        int $minimumStock,
        Money $cost,
    ): Product {
        return new Product(
            id: $id,
            tenantId: $tenantId,
            name: trim($name),
            sku: $sku,
            barcode: $barcode,
            category: $this->nullableTrim($category),
            brand: $this->nullableTrim($brand),
            supplier: $this->nullableTrim($supplier),
            minimumStock: $minimumStock,
            cost: $cost,
        );
    }

    private function nullableTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
