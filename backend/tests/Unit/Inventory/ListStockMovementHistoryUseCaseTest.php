<?php

namespace Tests\Unit\Inventory;

use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Contracts\StockMovementHistoryQuery;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos\ListStockMovementHistoryInput;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos\ListStockMovementHistoryItemOutput;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos\ListStockMovementHistoryOutput;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\ListStockMovementHistoryUseCase;
use PHPUnit\Framework\TestCase;

class ListStockMovementHistoryUseCaseTest extends TestCase
{
    public function test_it_lists_stock_movement_history(): void
    {
        $query = new ListStockMovementHistoryFakeQuery([
            new ListStockMovementHistoryItemOutput(
                id: '018f95f2-0f08-7f85-9b31-2d833a1a2f50',
                tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
                productId: '018f95f2-0f08-7f85-9b31-2d833a1a2f41',
                productName: 'Filtro de oleo',
                productSku: 'FO-001',
                userId: '018f95f2-0f08-7f85-9b31-2d833a1a2f43',
                direction: 'entry',
                type: 'purchase',
                quantity: 5,
                reason: 'Compra de reposicao',
                note: null,
                unitCostInCents: 2590,
                occurredAt: '2026-06-01T12:00:00+00:00',
            ),
        ]);

        $output = (new ListStockMovementHistoryUseCase($query))->execute(new ListStockMovementHistoryInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            direction: 'entry',
            limit: 10,
        ));

        $this->assertInstanceOf(ListStockMovementHistoryOutput::class, $output);
        $this->assertSame(1, $output->total());
        $this->assertSame('entry', $output->items[0]->direction);
        $this->assertSame('entry', $query->lastInput?->direction);
        $this->assertSame(10, $query->lastInput?->limit);
    }
}

final class ListStockMovementHistoryFakeQuery implements StockMovementHistoryQuery
{
    public ?ListStockMovementHistoryInput $lastInput = null;

    /**
     * @param  array<int, ListStockMovementHistoryItemOutput>  $items
     */
    public function __construct(private readonly array $items) {}

    public function search(ListStockMovementHistoryInput $input): array
    {
        $this->lastInput = $input;

        return $this->items;
    }
}
