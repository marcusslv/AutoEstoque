<?php

namespace Tests\Unit\Dashboard;

use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Contracts\DashboardQuery;
use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos\ViewDashboardInput;
use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos\ViewDashboardOutput;
use App\Modules\Dashboard\Application\UseCases\ViewDashboard\ViewDashboardUseCase;
use PHPUnit\Framework\TestCase;

class ViewDashboardUseCaseTest extends TestCase
{
    public function test_it_views_dashboard(): void
    {
        $query = new ViewDashboardFakeQuery;

        $output = (new ViewDashboardUseCase($query))->execute(new ViewDashboardInput(
            tenantId: '018f95f2-0f08-7f85-9b31-2d833a1a2f42',
            date: '2026-06-01',
            recentMovementsLimit: 3,
        ));

        $this->assertInstanceOf(ViewDashboardOutput::class, $output);
        $this->assertSame(4, $output->totalProducts);
        $this->assertSame(3, $query->lastInput?->recentMovementsLimit);
    }
}

final class ViewDashboardFakeQuery implements DashboardQuery
{
    public ?ViewDashboardInput $lastInput = null;

    public function get(ViewDashboardInput $input): ViewDashboardOutput
    {
        $this->lastInput = $input;

        return new ViewDashboardOutput(
            tenantId: $input->tenantId,
            date: $input->date,
            totalProducts: 4,
            productsBelowMinimum: 1,
            productsZeroStock: 1,
            totalStockValueInCents: 10000,
            todayMovements: 2,
            recentMovements: [],
        );
    }
}
