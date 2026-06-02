<?php

namespace App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts;

use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Contracts\MostConsumedProductsQuery;
use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos\ListMostConsumedProductsInput;
use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos\ListMostConsumedProductsOutput;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use Carbon\CarbonImmutable;

/**
 * @implements UseCase<ListMostConsumedProductsInput, ListMostConsumedProductsOutput>
 */
final readonly class ListMostConsumedProductsUseCase implements UseCase
{
    public function __construct(private MostConsumedProductsQuery $products) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof ListMostConsumedProductsInput);

        new TenantId($input->tenantId);
        $periodFrom = CarbonImmutable::parse($input->periodFrom)->startOfDay();
        $periodTo = CarbonImmutable::parse($input->periodTo)->endOfDay();

        return new ListMostConsumedProductsOutput(
            tenantId: $input->tenantId,
            periodFrom: $periodFrom->toDateString(),
            periodTo: $periodTo->toDateString(),
            items: $this->products->search($input),
        );
    }
}
