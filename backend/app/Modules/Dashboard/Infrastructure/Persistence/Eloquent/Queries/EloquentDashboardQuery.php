<?php

namespace App\Modules\Dashboard\Infrastructure\Persistence\Eloquent\Queries;

use App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Contracts\DashboardQuery;
use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos\DashboardRecentMovementOutput;
use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos\ViewDashboardInput;
use App\Modules\Dashboard\Application\UseCases\ViewDashboard\Dtos\ViewDashboardOutput;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Models\StockMovementModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

final class EloquentDashboardQuery implements DashboardQuery
{
    public function get(ViewDashboardInput $input): ViewDashboardOutput
    {
        $date = CarbonImmutable::parse($input->date);
        $dayStart = $date->startOfDay();
        $dayEnd = $date->endOfDay();

        return new ViewDashboardOutput(
            tenantId: $input->tenantId,
            date: $date->toDateString(),
            totalProducts: $this->totalProducts($input->tenantId),
            productsBelowMinimum: $this->productsBelowMinimum($input->tenantId),
            productsZeroStock: $this->productsZeroStock($input->tenantId),
            totalStockValueInCents: $this->totalStockValueInCents($input->tenantId),
            todayMovements: $this->todayMovements($input->tenantId, $dayStart, $dayEnd),
            recentMovements: $this->recentMovements($input->tenantId, $dayStart, $dayEnd, $input->recentMovementsLimit),
        );
    }

    private function totalProducts(string $tenantId): int
    {
        return ProductModel::query()
            ->where('tenant_id', $tenantId)
            ->count();
    }

    private function productsBelowMinimum(string $tenantId): int
    {
        return ProductModel::query()
            ->leftJoin('inventory_items', function ($join): void {
                $join->on('inventory_items.product_id', '=', 'products.id')
                    ->on('inventory_items.tenant_id', '=', 'products.tenant_id');
            })
            ->where('products.tenant_id', $tenantId)
            ->where('products.minimum_stock', '>', 0)
            ->whereRaw('COALESCE(inventory_items.current_stock, 0) > 0')
            ->whereRaw('COALESCE(inventory_items.current_stock, 0) <= products.minimum_stock')
            ->count();
    }

    private function productsZeroStock(string $tenantId): int
    {
        return ProductModel::query()
            ->leftJoin('inventory_items', function ($join): void {
                $join->on('inventory_items.product_id', '=', 'products.id')
                    ->on('inventory_items.tenant_id', '=', 'products.tenant_id');
            })
            ->where('products.tenant_id', $tenantId)
            ->whereRaw('COALESCE(inventory_items.current_stock, 0) = 0')
            ->count();
    }

    private function totalStockValueInCents(string $tenantId): int
    {
        return (int) ProductModel::query()
            ->leftJoin('inventory_items', function ($join): void {
                $join->on('inventory_items.product_id', '=', 'products.id')
                    ->on('inventory_items.tenant_id', '=', 'products.tenant_id');
            })
            ->where('products.tenant_id', $tenantId)
            ->selectRaw('COALESCE(SUM(COALESCE(inventory_items.current_stock, 0) * products.cost_in_cents), 0) as total')
            ->value('total');
    }

    private function todayMovements(string $tenantId, CarbonImmutable $dayStart, CarbonImmutable $dayEnd): int
    {
        return StockMovementModel::query()
            ->where('tenant_id', $tenantId)
            ->whereBetween('occurred_at', [$dayStart, $dayEnd])
            ->count();
    }

    /**
     * @return array<int, DashboardRecentMovementOutput>
     */
    private function recentMovements(
        string $tenantId,
        CarbonImmutable $dayStart,
        CarbonImmutable $dayEnd,
        int $limit,
    ): array {
        return StockMovementModel::query()
            ->select([
                'stock_movements.id',
                'stock_movements.product_id',
                'products.name as product_name',
                'products.sku as product_sku',
                'stock_movements.direction',
                'stock_movements.type',
                'stock_movements.quantity',
                'stock_movements.reason',
                'stock_movements.occurred_at',
            ])
            ->join('products', 'products.id', '=', 'stock_movements.product_id')
            ->where('stock_movements.tenant_id', $tenantId)
            ->whereBetween('stock_movements.occurred_at', [$dayStart, $dayEnd])
            ->orderByDesc('stock_movements.occurred_at')
            ->orderByDesc('stock_movements.created_at')
            ->limit($limit)
            ->get()
            ->map(fn (Model $movement): DashboardRecentMovementOutput => new DashboardRecentMovementOutput(
                id: (string) $movement->getAttribute('id'),
                productId: (string) $movement->getAttribute('product_id'),
                productName: (string) $movement->getAttribute('product_name'),
                productSku: (string) $movement->getAttribute('product_sku'),
                direction: (string) $movement->getAttribute('direction'),
                type: (string) $movement->getAttribute('type'),
                quantity: (int) $movement->getAttribute('quantity'),
                reason: (string) $movement->getAttribute('reason'),
                occurredAt: CarbonImmutable::parse($movement->getAttribute('occurred_at'))->toAtomString(),
            ))
            ->all();
    }
}
