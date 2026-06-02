<?php

namespace Tests\Unit\Dashboard;

use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Contracts\MostConsumedProductsQuery;
use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos\ListMostConsumedProductsInput;
use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos\ListMostConsumedProductsOutput;
use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos\MostConsumedProductOutput;
use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\ListMostConsumedProductsUseCase;
use PHPUnit\Framework\TestCase;

class ListMostConsumedProductsUseCaseTest extends TestCase
{
    public function test_it_lists_most_consumed_products(): void
    {
        $query = new ListMostConsumedProductsFakeQuery([
            new MostConsumedProductOutput(
                productId: '018f95f2-0f08-7f85-9b31-2d833a1a2f41',
                productName: 'Filtro de oleo',
                productSku: 'FO-001',
                totalQuantity: 5,
                movementsCount: 2,
            ),
        ]);

        $output = (new ListMostConsumedProductsUseCase($query))->execute(new ListMostConsumedProductsInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            periodFrom: '2026-06-01',
            periodTo: '2026-06-30',
            limit: 10,
        ));

        $this->assertInstanceOf(ListMostConsumedProductsOutput::class, $output);
        $this->assertSame(1, $output->total());
        $this->assertSame(5, $output->items[0]->totalQuantity);
        $this->assertSame(10, $query->lastInput?->limit);
    }
}

final class ListMostConsumedProductsFakeQuery implements MostConsumedProductsQuery
{
    public ?ListMostConsumedProductsInput $lastInput = null;

    /**
     * @param  array<int, MostConsumedProductOutput>  $items
     */
    public function __construct(private readonly array $items) {}

    public function search(ListMostConsumedProductsInput $input): array
    {
        $this->lastInput = $input;

        return $this->items;
    }
}
