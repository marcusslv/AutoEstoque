<?php

namespace App\Modules\Catalog\Application\UseCases\UpdateProduct\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class UpdateProductInput implements InputDto
{
    public function __construct(
        public string $productId,
        public string $tenantId,
        public string $name,
        public string $sku,
        public ?string $barcode,
        public ?string $category,
        public ?string $brand,
        public ?string $supplier,
        public int $minimumStock,
        public int $costInCents,
        public string $currency = 'BRL',
    ) {}
}
