<?php

namespace Tests\Unit\Inventory;

use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Contracts\MinimumStockAlertQuery;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos\GenerateMinimumStockAlertsInput;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos\GenerateMinimumStockAlertsOutput;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos\MinimumStockAlertOutput;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\GenerateMinimumStockAlertsUseCase;
use PHPUnit\Framework\TestCase;

class GenerateMinimumStockAlertsUseCaseTest extends TestCase
{
    public function test_it_generates_minimum_stock_alerts(): void
    {
        $query = new GenerateMinimumStockAlertsFakeQuery([
            new MinimumStockAlertOutput(
                productId: '018f95f2-0f08-7f85-9b31-2d833a1a2f41',
                productName: 'Filtro de oleo',
                productSku: 'FO-001',
                currentStock: 2,
                minimumStock: 5,
                shortageQuantity: 3,
            ),
        ]);

        $output = (new GenerateMinimumStockAlertsUseCase($query))->execute(new GenerateMinimumStockAlertsInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            limit: 10,
        ));

        $this->assertInstanceOf(GenerateMinimumStockAlertsOutput::class, $output);
        $this->assertSame(1, $output->total());
        $this->assertSame(3, $output->alerts[0]->shortageQuantity);
        $this->assertSame(10, $query->lastInput?->limit);
    }
}

final class GenerateMinimumStockAlertsFakeQuery implements MinimumStockAlertQuery
{
    public ?GenerateMinimumStockAlertsInput $lastInput = null;

    /**
     * @param  array<int, MinimumStockAlertOutput>  $alerts
     */
    public function __construct(private readonly array $alerts) {}

    public function search(GenerateMinimumStockAlertsInput $input): array
    {
        $this->lastInput = $input;

        return $this->alerts;
    }
}
