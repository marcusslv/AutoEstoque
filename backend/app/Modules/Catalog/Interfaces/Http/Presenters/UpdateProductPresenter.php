<?php

namespace App\Modules\Catalog\Interfaces\Http\Presenters;

use App\Modules\Catalog\Application\UseCases\UpdateProduct\UpdateProductOutput;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Interfaces\Http\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;

final class UpdateProductPresenter implements JsonPresenter
{
    public function present(OutputDto $output): JsonResponse
    {
        assert($output instanceof UpdateProductOutput);

        return response()->json([
            'data' => [
                'id' => $output->id,
                'tenant_id' => $output->tenantId,
                'name' => $output->name,
                'sku' => $output->sku,
                'barcode' => $output->barcode,
                'category' => $output->category,
                'brand' => $output->brand,
                'supplier' => $output->supplier,
                'minimum_stock' => $output->minimumStock,
                'cost_in_cents' => $output->costInCents,
                'currency' => $output->currency,
            ],
        ]);
    }
}
