<?php

namespace App\Modules\Identity\Application\UseCases\ManageWorkshopUsers;

use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Contracts\WorkshopUserRepository;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\ListWorkshopUsersInput;
use App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos\ListWorkshopUsersOutput;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

/**
 * @implements UseCase<ListWorkshopUsersInput, ListWorkshopUsersOutput>
 */
final readonly class ListWorkshopUsersUseCase implements UseCase
{
    public function __construct(private WorkshopUserRepository $users) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof ListWorkshopUsersInput);

        $tenantId = new TenantId($input->tenantId);

        return new ListWorkshopUsersOutput(
            $this->users->listByTenant($tenantId),
        );
    }
}
