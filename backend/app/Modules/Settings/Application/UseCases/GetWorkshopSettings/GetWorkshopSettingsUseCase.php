<?php

namespace App\Modules\Settings\Application\UseCases\GetWorkshopSettings;

use App\Modules\Settings\Application\UseCases\GetWorkshopSettings\Dtos\GetWorkshopSettingsInput;
use App\Modules\Settings\Domain\Repositories\WorkshopSettingsRepository;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

/**
 * @implements UseCase<GetWorkshopSettingsInput, OutputDto>
 */
final readonly class GetWorkshopSettingsUseCase implements UseCase
{
    public function __construct(private WorkshopSettingsRepository $settings) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof GetWorkshopSettingsInput);

        return $this->settings->getOrCreate(new TenantId($input->tenantId));
    }
}
