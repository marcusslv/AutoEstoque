<?php

namespace App\Modules\Catalog\Application\UseCases\ListStock\Dtos;

final readonly class ListStockItemOutput
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $name,
        public string $sku,
        public ?string $barcode,
        public ?string $category,
        public ?string $brand,
        public ?string $supplier,
        public int $minimumStock,
        public int $currentStock,
        public string $stockStatus,
        public int $costInCents,
        public string $currency,
    ) {}
}
