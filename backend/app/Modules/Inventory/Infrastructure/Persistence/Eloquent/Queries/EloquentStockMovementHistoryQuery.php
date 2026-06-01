<?php

namespace App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Queries;

use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Contracts\StockMovementHistoryQuery;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos\ListStockMovementHistoryInput;
use App\Modules\Inventory\Application\UseCases\ListStockMovementHistory\Dtos\ListStockMovementHistoryItemOutput;
use App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Models\StockMovementModel;
use Carbon\CarbonImmutable;

final class EloquentStockMovementHistoryQuery implements StockMovementHistoryQuery
{
    public function search(ListStockMovementHistoryInput $input): array
    {
        $query = StockMovementModel::query()
            ->select([
                'stock_movements.id',
                'stock_movements.tenant_id',
                'stock_movements.product_id',
                'products.name as product_name',
                'products.sku as product_sku',
                'stock_movements.user_id',
                'stock_movements.direction',
                'stock_movements.type',
                'stock_movements.quantity',
                'stock_movements.reason',
                'stock_movements.note',
                'stock_movements.unit_cost_in_cents',
                'stock_movements.occurred_at',
            ])
            ->join('products', 'products.id', '=', 'stock_movements.product_id')
            ->where('stock_movements.tenant_id', $input->tenantId);

        if ($input->productId !== null) {
            $query->where('stock_movements.product_id', $input->productId);
        }

        if ($input->direction !== null) {
            $query->where('stock_movements.direction', $input->direction);
        }

        if ($input->type !== null) {
            $query->where('stock_movements.type', $input->type);
        }

        if ($input->userId !== null) {
            $query->where('stock_movements.user_id', $input->userId);
        }

        if ($input->occurredFrom !== null) {
            $query->where('stock_movements.occurred_at', '>=', CarbonImmutable::parse($input->occurredFrom)->startOfDay());
        }

        if ($input->occurredTo !== null) {
            $query->where('stock_movements.occurred_at', '<=', CarbonImmutable::parse($input->occurredTo)->endOfDay());
        }

        return $query
            ->orderByDesc('stock_movements.occurred_at')
            ->orderByDesc('stock_movements.created_at')
            ->limit($input->limit)
            ->get()
            ->map(fn (StockMovementModel $movement): ListStockMovementHistoryItemOutput => new ListStockMovementHistoryItemOutput(
                id: (string) $movement->getAttribute('id'),
                tenantId: (string) $movement->getAttribute('tenant_id'),
                productId: (string) $movement->getAttribute('product_id'),
                productName: (string) $movement->getAttribute('product_name'),
                productSku: (string) $movement->getAttribute('product_sku'),
                userId: (string) $movement->getAttribute('user_id'),
                direction: (string) $movement->getAttribute('direction'),
                type: (string) $movement->getAttribute('type'),
                quantity: (int) $movement->getAttribute('quantity'),
                reason: (string) $movement->getAttribute('reason'),
                note: $movement->getAttribute('note'),
                unitCostInCents: $movement->getAttribute('unit_cost_in_cents') === null
                    ? null
                    : (int) $movement->getAttribute('unit_cost_in_cents'),
                occurredAt: CarbonImmutable::parse($movement->getAttribute('occurred_at'))->toAtomString(),
            ))
            ->all();
    }
}
