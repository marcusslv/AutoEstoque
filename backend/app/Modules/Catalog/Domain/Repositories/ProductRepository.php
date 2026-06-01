<?php

namespace App\Modules\Catalog\Domain\Repositories;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\ProductId;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

interface ProductRepository
{
    public function findById(TenantId $tenantId, ProductId $productId): ?Product;

    public function existsBySku(TenantId $tenantId, Sku $sku): bool;

    public function existsByBarcode(TenantId $tenantId, Barcode $barcode): bool;

    public function existsBySkuIgnoringProduct(TenantId $tenantId, Sku $sku, ProductId $productId): bool;

    public function existsByBarcodeIgnoringProduct(TenantId $tenantId, Barcode $barcode, ProductId $productId): bool;

    public function save(Product $product): void;

    public function update(Product $product): void;
}
