<?php

namespace App\Modules\Identity\Application\UseCases\ManageWorkshopUsers;

use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Contracts\WorkshopUserRepository;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\CreateWorkshopUserInput;
use App\Modules\Identity\Domain\Exceptions\DuplicatedUserEmailException;
use App\Modules\Identity\Domain\Exceptions\UserLimitReachedException;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

/**
 * @implements UseCase<CreateWorkshopUserInput, OutputDto>
 */
final readonly class CreateWorkshopUserUseCase implements UseCase
{
    private const STARTER_MAX_ACTIVE_USERS = 3;

    public function __construct(private WorkshopUserRepository $users) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof CreateWorkshopUserInput);

        $tenantId = new TenantId($input->tenantId);

        if ($this->users->existsByEmail($input->email)) {
            throw new DuplicatedUserEmailException;
        }

        if ($input->status === 'active' && $this->users->activeUsersCount($tenantId) >= self::STARTER_MAX_ACTIVE_USERS) {
            throw new UserLimitReachedException;
        }

        return $this->users->create($input);
    }
}
