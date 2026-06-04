<?php

namespace App\Modules\Settings\Application\UseCases\UpdateWorkshopSettings;

use App\Modules\Settings\Application\UseCases\UpdateWorkshopSettings\Dtos\UpdateWorkshopSettingsInput;
use App\Modules\Settings\Domain\Repositories\WorkshopSettingsRepository;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

/**
 * @implements UseCase<UpdateWorkshopSettingsInput, OutputDto>
 */
final readonly class UpdateWorkshopSettingsUseCase implements UseCase
{
    public function __construct(private WorkshopSettingsRepository $settings) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof UpdateWorkshopSettingsInput);

        new TenantId($input->tenantId);

        return $this->settings->update($input);
    }
}
