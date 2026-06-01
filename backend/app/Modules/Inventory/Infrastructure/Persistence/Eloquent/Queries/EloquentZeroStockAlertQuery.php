<?php

namespace App\Modules\Inventory\Infrastructure\Persistence\Eloquent\Queries;

use App\Modules\Catalog\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Contracts\ZeroStockAlertQuery;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos\GenerateZeroStockAlertsInput;
use App\Modules\Inventory\Application\UseCases\GenerateZeroStockAlerts\Dtos\ZeroStockAlertOutput;
use Illuminate\Database\Eloquent\Model;

final class EloquentZeroStockAlertQuery implements ZeroStockAlertQuery
{
    public function search(GenerateZeroStockAlertsInput $input): array
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
            ->whereRaw('COALESCE(inventory_items.current_stock, 0) = 0')
            ->orderBy('products.name')
            ->limit($input->limit)
            ->get()
            ->map(fn (Model $product): ZeroStockAlertOutput => new ZeroStockAlertOutput(
                productId: (string) $product->getAttribute('id'),
                productName: (string) $product->getAttribute('name'),
                productSku: (string) $product->getAttribute('sku'),
                currentStock: (int) ($product->getAttribute('current_stock') ?? 0),
                minimumStock: (int) $product->getAttribute('minimum_stock'),
            ))
            ->all();
    }
}
