<?php

namespace App\Modules\Settings\Application\UseCases\GetWorkshopSettings\Dtos;

use App\Modules\Shared\Application\Contracts\InputDto;

final readonly class GetWorkshopSettingsInput implements InputDto
{
    public function __construct(public string $tenantId) {}
}
