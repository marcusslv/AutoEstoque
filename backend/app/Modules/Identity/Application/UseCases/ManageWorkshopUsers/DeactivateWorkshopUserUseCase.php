<?php

namespace App\Modules\Identity\Application\UseCases\ManageWorkshopUsers;

use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Contracts\WorkshopUserRepository;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\DeactivateWorkshopUserInput;
use App\Modules\Identity\Domain\Exceptions\UserNotFoundException;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

/**
 * @implements UseCase<DeactivateWorkshopUserInput, OutputDto>
 */
final readonly class DeactivateWorkshopUserUseCase implements UseCase
{
    public function __construct(private WorkshopUserRepository $users) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof DeactivateWorkshopUserInput);

        $tenantId = new TenantId($input->tenantId);

        if ($this->users->findById($tenantId, $input->userId) === null) {
            throw new UserNotFoundException;
        }

        return $this->users->deactivate($tenantId, $input->userId);
    }
}
