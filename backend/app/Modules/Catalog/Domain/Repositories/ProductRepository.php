<?php

namespace App\Modules\Catalog\Domain\Repositories;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\ValueObjects\Barcode;
use App\Modules\Catalog\Domain\ValueObjects\Sku;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

interface ProductRepository
{
    public function existsBySku(TenantId $tenantId, Sku $sku): bool;

    public function existsByBarcode(TenantId $tenantId, Barcode $barcode): bool;

    public function save(Product $product): void;
}
