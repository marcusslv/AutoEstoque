<?php

namespace App\Modules\Identity\Application\UseCases\ManageWorkshopUsers\Dtos;

use App\Modules\Shared\Application\Contracts\OutputDto;

final readonly class ListWorkshopUsersOutput implements OutputDto
{
    /**
     * @param  array<int, WorkshopUserOutput>  $users
     */
    public function __construct(public array $users) {}

    public function total(): int
    {
        return count($this->users);
    }
}
