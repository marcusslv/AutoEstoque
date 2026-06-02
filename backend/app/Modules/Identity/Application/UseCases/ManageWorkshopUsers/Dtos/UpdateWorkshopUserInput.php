<?php

namespace App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class UpdateWorkshopUserInput implements InputDto
{
    public function __construct(
        public string $tenantId,
        public string $userId,
        public string $name,
        public string $role,
        public string $status,
    ) {}
}
