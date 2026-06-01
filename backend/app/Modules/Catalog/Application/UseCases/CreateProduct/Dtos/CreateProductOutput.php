<?php

namespace App\Modules\Catalog\Application\UseCases\CreateProduct\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class CreateProductOutput implements OutputDto
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
        public int $costInCents,
        public string $currency,
    ) {}
}
