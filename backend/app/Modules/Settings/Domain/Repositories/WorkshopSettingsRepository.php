<?php

namespace App\Modules\Settings\Domain\Repositories;

use App\Modules\Settings\Application\UseCases\GetWorkshopSettings\Dtos\WorkshopSettingsOutput;
use App\Modules\Settings\Application\UseCases\UpdateWorkshopSettings\Dtos\UpdateWorkshopSettingsInput;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

interface WorkshopSettingsRepository
{
    public function getOrCreate(TenantId $tenantId): WorkshopSettingsOutput;

    public function update(UpdateWorkshopSettingsInput $input): WorkshopSettingsOutput;
}
