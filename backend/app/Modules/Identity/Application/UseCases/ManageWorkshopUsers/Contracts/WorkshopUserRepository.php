<?php

namespace App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Contracts;

use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\CreateWorkshopUserInput;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\UpdateWorkshopUserInput;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\WorkshopUserOutput;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

interface WorkshopUserRepository
{
    /**
     * @return array<int, WorkshopUserOutput>
     */
    public function listByTenant(TenantId $tenantId): array;

    public function findById(TenantId $tenantId, string $userId): ?WorkshopUserOutput;

    public function existsByEmail(string $email): bool;

    public function activeUsersCount(TenantId $tenantId): int;

    public function create(CreateWorkshopUserInput $input): WorkshopUserOutput;

    public function update(UpdateWorkshopUserInput $input): WorkshopUserOutput;

    public function deactivate(TenantId $tenantId, string $userId): WorkshopUserOutput;
}
