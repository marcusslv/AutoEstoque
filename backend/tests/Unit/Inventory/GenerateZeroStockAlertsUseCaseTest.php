<?php

namespace Tests\Unit\Inventory;

use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Contracts\ZeroStockAlertQuery;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos\GenerateZeroStockAlertsInput;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos\GenerateZeroStockAlertsOutput;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos\ZeroStockAlertOutput;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\GenerateZeroStockAlertsUseCase;
use PHPUnit\Framework\TestCase;

class GenerateZeroStockAlertsUseCaseTest extends TestCase
{
    public function test_it_generates_zero_stock_alerts(): void
    {
        $query = new GenerateZeroStockAlertsFakeQuery([
            new ZeroStockAlertOutput(
                productId: '018f95f2-0f08-7f85-9b31-2d833a1a2f41',
                productName: 'Filtro de oleo',
                productSku: 'FO-001',
                currentStock: 0,
                minimumStock: 2,
            ),
        ]);

        $output = (new GenerateZeroStockAlertsUseCase($query))->execute(new GenerateZeroStockAlertsInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            limit: 10,
        ));

        $this->assertInstanceOf(GenerateZeroStockAlertsOutput::class, $output);
        $this->assertSame(1, $output->total());
        $this->assertSame(0, $output->alerts[0]->currentStock);
        $this->assertSame(10, $query->lastInput?->limit);
    }
}

final class GenerateZeroStockAlertsFakeQuery implements ZeroStockAlertQuery
{
    public ?GenerateZeroStockAlertsInput $lastInput = null;

    /**
     * @param  array<int, ZeroStockAlertOutput>  $alerts
     */
    public function __construct(private readonly array $alerts) {}

    public function search(GenerateZeroStockAlertsInput $input): array
    {
        $this->lastInput = $input;

        return $this->alerts;
    }
}
