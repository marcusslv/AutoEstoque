<?php

namespace App\Modules\Workshop\Application\UseCases\AddPartToServiceOrder\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class AddPartToServiceOrderOutput implements OutputDto
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $serviceOrderId,
        public string $productId,
        public string $addedByUserId,
        public int $quantity,
    ) {}
}
