<?php

namespace App\Modules\Dashboard\Application\UseCases\ViewDashboard;

use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Contracts\DashboardQuery;
use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos\ViewDashboardInput;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use Carbon\CarbonImmutable;

/**
 * @implements UseCase<ViewDashboardInput, OutputDto>
 */
final readonly class ViewDashboardUseCase implements UseCase
{
    public function __construct(private DashboardQuery $dashboard) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof ViewDashboardInput);

        new TenantId($input->tenantId);
        CarbonImmutable::parse($input->date);

        return $this->dashboard->get($input);
    }
}
