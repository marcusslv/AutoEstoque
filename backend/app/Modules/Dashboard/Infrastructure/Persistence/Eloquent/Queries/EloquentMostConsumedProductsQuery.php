<?php

namespace App\Modules\Dashboard\Infrastructure\Persistence\Eloquent\Queries;

use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Contracts\MostConsumedProductsQuery;
use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos\ListMostConsumedProductsInput;
use App\Modules\Dashboard\Application\UseCases\ListMostConsumedProducts\Dtos\MostConsumedProductOutput;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Models\StockMovementModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

final class EloquentMostConsumedProductsQuery implements MostConsumedProductsQuery
{
    public function search(ListMostConsumedProductsInput $input): array
    {
        $periodFrom = CarbonImmutable::parse($input->periodFrom)->startOfDay();
        $periodTo = CarbonImmutable::parse($input->periodTo)->endOfDay();

        return StockMovementModel::query()
            ->selectRaw('
                stock_movements.product_id,
                products.name as product_name,
                products.sku as product_sku,
                SUM(stock_movements.quantity) as total_quantity,
                COUNT(stock_movements.id) as movements_count
            ')
            ->join('products', 'products.id', '=', 'stock_movements.product_id')
            ->where('stock_movements.tenant_id', $input->tenantId)
            ->where('stock_movements.direction', 'output')
            ->whereBetween('stock_movements.occurred_at', [$periodFrom, $periodTo])
            ->groupBy('stock_movements.product_id', 'products.name', 'products.sku')
            ->orderByDesc('total_quantity')
            ->orderBy('products.name')
            ->limit($input->limit)
            ->get()
            ->map(fn (Model $item): MostConsumedProductOutput => new MostConsumedProductOutput(
                productId: (string) $item->getAttribute('product_id'),
                productName: (string) $item->getAttribute('product_name'),
                productSku: (string) $item->getAttribute('product_sku'),
                totalQuantity: (int) $item->getAttribute('total_quantity'),
                movementsCount: (int) $item->getAttribute('movements_count'),
            ))
            ->all();
    }
}
