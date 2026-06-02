<?php

namespace Tests\Unit\Identity;

use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Contracts\WorkshopUserRepository;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\CreateWorkshopUserUseCase;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\CreateWorkshopUserInput;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\UpdateWorkshopUserInput;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\WorkshopUserOutput;
use App\Modules\Identity\Domain\Exceptions\DuplicatedUserEmailException;
use App\Modules\Identity\Domain\Exceptions\UserLimitReachedException;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use PHPUnit\Framework\TestCase;

class CreateWorkshopUserUseCaseTest extends TestCase
{
    public function test_it_rejects_duplicated_email(): void
    {
        $repository = new CreateWorkshopUserFakeRepository;
        $repository->emailExists = true;

        $this->expectException(DuplicatedUserEmailException::class);

        (new CreateWorkshopUserUseCase($repository))->execute($this->input());
    }

    public function test_it_rejects_user_limit(): void
    {
        $repository = new CreateWorkshopUserFakeRepository;
        $repository->activeUsers = 3;

        $this->expectException(UserLimitReachedException::class);

        (new CreateWorkshopUserUseCase($repository))->execute($this->input());
    }

    private function input(): CreateWorkshopUserInput
    {
        return new CreateWorkshopUserInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            name: 'Mecanico Oficina',
            email: 'mecanico@oficina.com',
            password: 'secret123',
            role: 'mechanic',
            status: 'active',
        );
    }
}

final class CreateWorkshopUserFakeRepository implements WorkshopUserRepository
{
    public bool $emailExists = false;

    public int $activeUsers = 0;

    public function listByTenant(TenantId $tenantId): array
    {
        return [];
    }

    public function findById(TenantId $tenantId, string $userId): ?WorkshopUserOutput
    {
        return null;
    }

    public function existsByEmail(string $email): bool
    {
        return $this->emailExists;
    }

    public function activeUsersCount(TenantId $tenantId): int
    {
        return $this->activeUsers;
    }

    public function create(CreateWorkshopUserInput $input): WorkshopUserOutput
    {
        return new WorkshopUserOutput(
            id: '1',
            tenantId: $input->tenantId,
            name: $input->name,
            email: $input->email,
            status: $input->status,
            role: $input->role,
        );
    }

    public function update(UpdateWorkshopUserInput $input): WorkshopUserOutput
    {
        throw new \BadMethodCallException;
    }

    public function deactivate(TenantId $tenantId, string $userId): WorkshopUserOutput
    {
        throw new \BadMethodCallException;
    }
}
