<?php

namespace App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts;

use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Contracts\MinimumStockAlertQuery;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos\GenerateMinimumStockAlertsInput;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos\GenerateMinimumStockAlertsOutput;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

/**
 * @implements UseCase<GenerateMinimumStockAlertsInput, GenerateMinimumStockAlertsOutput>
 */
final readonly class GenerateMinimumStockAlertsUseCase implements UseCase
{
    public function __construct(private MinimumStockAlertQuery $alerts) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof GenerateMinimumStockAlertsInput);

        new TenantId($input->tenantId);

        return new GenerateMinimumStockAlertsOutput(
            $this->alerts->search($input),
        );
    }
}
