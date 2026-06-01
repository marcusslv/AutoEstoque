<?php

namespace App\Modules\Catalog\Application\UseCases\ListStock;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class ListStockInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public ?string $term = null,
    ) {}
}
