<?php

namespace App\Modules\Catalog\Interfaces\Http\Presenters;

use App\Modules\Catalog\Application\UseCases\ListStock\Dtos\ListStockItemOutput;
use App\Modules\Catalog\Application\UseCases\ListStock\Dtos\ListStockOutput;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;

final class ListStockPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof ListStockOutput);

        return response()->json([
            'data' => array_map(
                fn (ListStockItemOutput $item): array => [
                    'id' => $item->id,
                    'tenant_id' => $item->tenantId,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'barcode' => $item->barcode,
                    'category' => $item->category,
                    'brand' => $item->brand,
                    'supplier' => $item->supplier,
                    'minimum_stock' => $item->minimumStock,
                    'current_stock' => $item->currentStock,
                    'stock_status' => $item->stockStatus,
                    'cost_in_cents' => $item->costInCents,
                    'currency' => $item->currency,
                ],
                $output->items,
            ),
            'meta' => [
                'total' => $output->total(),
            ],
        ]);
    }
}
