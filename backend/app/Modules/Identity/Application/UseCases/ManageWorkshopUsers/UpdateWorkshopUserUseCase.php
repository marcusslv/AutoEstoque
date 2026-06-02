<?php

namespace App\Modules\Identity\Application\UseCases\ManageWorkshopUsers;

use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Contracts\WorkshopUserRepository;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\UpdateWorkshopUserInput;
use App\Modules\Identity\Domain\Exceptions\UserLimitReachedException;
use App\Modules\Identity\Domain\Exceptions\UserNotFoundException;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

/**
 * @implements UseCase<UpdateWorkshopUserInput, OutputDto>
 */
final readonly class UpdateWorkshopUserUseCase implements UseCase
{
    private const STARTER_MAX_ACTIVE_USERS = 3;

    public function __construct(private WorkshopUserRepository $users) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof UpdateWorkshopUserInput);

        $tenantId = new TenantId($input->tenantId);
        $currentUser = $this->users->findById($tenantId, $input->userId);

        if ($currentUser === null) {
            throw new UserNotFoundException;
        }

        if (
            $input->status === 'active'
            && $currentUser->status !== 'active'
            && $this->users->activeUsersCount($tenantId) >= self::STARTER_MAX_ACTIVE_USERS
        ) {
            throw new UserLimitReachedException;
        }

        return $this->users->update($input);
    }
}
