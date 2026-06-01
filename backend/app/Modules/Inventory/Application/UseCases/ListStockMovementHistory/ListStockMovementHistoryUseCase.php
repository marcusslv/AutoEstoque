<?php

namespace App\Modules\Inventory\Application\UseCases\ListStockMovementHistory;

use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Contracts\StockMovementHistoryQuery;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos\ListStockMovementHistoryInput;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos\ListStockMovementHistoryOutput;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

/**
 * @implements UseCase<ListStockMovementHistoryInput, ListStockMovementHistoryOutput>
 */
final readonly class ListStockMovementHistoryUseCase implements UseCase
{
    public function __construct(private StockMovementHistoryQuery $history) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof ListStockMovementHistoryInput);

        new TenantId($input->tenantId);

        return new ListStockMovementHistoryOutput(
            $this->history->search($input),
        );
    }
}
