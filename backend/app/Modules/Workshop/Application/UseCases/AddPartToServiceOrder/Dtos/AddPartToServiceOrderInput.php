<?php

namespace App\Modules\Workshop\Application\UseCases\AddPartToServiceOrder\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class AddPartToServiceOrderInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public string $serviceOrderId,
        public string $productId,
        public string $addedByUserId,
        public int $quantity,
    ) {}
}
