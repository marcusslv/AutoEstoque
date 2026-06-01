<?php

namespace App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts;

use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Contracts\ZeroStockAlertQuery;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos\GenerateZeroStockAlertsInput;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos\GenerateZeroStockAlertsOutput;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

/**
 * @implements UseCase<GenerateZeroStockAlertsInput, GenerateZeroStockAlertsOutput>
 */
final readonly class GenerateZeroStockAlertsUseCase implements UseCase
{
    public function __construct(private ZeroStockAlertQuery $alerts) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof GenerateZeroStockAlertsInput);

        new TenantId($input->tenantId);

        return new GenerateZeroStockAlertsOutput(
            $this->alerts->search($input),
        );
    }
}
