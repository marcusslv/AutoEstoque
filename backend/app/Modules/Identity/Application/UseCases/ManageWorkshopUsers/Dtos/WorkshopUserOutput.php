<?php

namespace App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class WorkshopUserOutput implements OutputDto
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $name,
        public string $email,
        public string $status,
        public string $role,
    ) {}
}
