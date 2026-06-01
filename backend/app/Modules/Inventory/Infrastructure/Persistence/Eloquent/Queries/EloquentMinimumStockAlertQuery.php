<?php

namespace App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Queries;

use App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Contracts\MinimumStockAlertQuery;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos\GenerateMinimumStockAlertsInput;
use App\Modules\Inventory\Application\UseCases\GenerateMinimumStockAlerts\Dtos\MinimumStockAlertOutput;
use Illuminate\Database\Eloquent\Model;

final class EloquentMinimumStockAlertQuery implements MinimumStockAlertQuery
{
    public function search(GenerateMinimumStockAlertsInput $input): array
    {
        return ProductModel::query()
            ->select([
                'products.id',
                'products.name',
                'products.sku',
                'products.minimum_stock',
                'inventory_items.current_stock',
            ])
            ->leftJoin('inventory_items', function ($join): void {
                $join->on('inventory_items.product_id', '=', 'products.id')
                    ->on('inventory_items.tenant_id', '=', 'products.tenant_id');
            })
            ->where('products.tenant_id', $input->tenantId)
            ->where('products.minimum_stock', '>', 0)
            ->whereRaw('COALESCE(inventory_items.current_stock, 0) > 0')
            ->whereRaw('COALESCE(inventory_items.current_stock, 0) <= products.minimum_stock')
            ->orderByRaw('(products.minimum_stock - COALESCE(inventory_items.current_stock, 0)) desc')
            ->orderBy('products.name')
            ->limit($input->limit)
            ->get()
            ->map(fn (Model $product): MinimumStockAlertOutput => $this->toOutput($product))
            ->all();
    }

    private function toOutput(Model $product): MinimumStockAlertOutput
    {
        $currentStock = (int) ($product->getAttribute('current_stock') ?? 0);
        $minimumStock = (int) $product->getAttribute('minimum_stock');

        return new MinimumStockAlertOutput(
            productId: (string) $product->getAttribute('id'),
            productName: (string) $product->getAttribute('name'),
            productSku: (string) $product->getAttribute('sku'),
            currentStock: $currentStock,
            minimumStock: $minimumStock,
            shortageQuantity: max(0, $minimumStock - $currentStock),
        );
    }
}
