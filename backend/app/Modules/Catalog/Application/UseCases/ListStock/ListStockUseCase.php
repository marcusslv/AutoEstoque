<?php

namespace App\Modules\Catalog\Application\UseCases\ListStock;

use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Inventory\Domain\Repositories\InventoryItemRepository;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;

/**
 * @implements UseCase<ListStockInput, ListStockOutput>
 */
final readonly class ListStockUseCase implements UseCase
{
    public function __construct(
        private ProductRepository $products,
        private InventoryItemRepository $inventoryItems,
    ) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof ListStockInput);

        $tenantId = new TenantId($input->tenantId);

        $items = array_map(
            fn (Product $product): ListStockItemOutput => $this->toOutput($tenantId, $product),
            $this->products->search($tenantId, $input->term),
        );

        return new ListStockOutput($items);
    }

    private function toOutput(TenantId $tenantId, Product $product): ListStockItemOutput
    {
        $inventoryItem = $this->inventoryItems->findByProductId(
            $tenantId,
            new StockProductId($product->id()->value),
        );
        $currentStock = $inventoryItem?->currentStock()->value ?? 0;

        return new ListStockItemOutput(
            id: $product->id()->value,
            tenantId: $product->tenantId()->value,
            name: $product->name(),
            sku: $product->sku()->value,
            barcode: $product->barcode()->value,
            category: $product->category(),
            brand: $product->brand(),
            supplier: $product->supplier(),
            minimumStock: $product->minimumStock(),
            currentStock: $currentStock,
            stockStatus: $this->stockStatus($currentStock, $product->minimumStock()),
            costInCents: $product->cost()->amountInCents,
            currency: $product->cost()->currency,
        );
    }

    private function stockStatus(int $currentStock, int $minimumStock): string
    {
        if ($currentStock === 0) {
            return 'zero';
        }

        if ($currentStock < $minimumStock) {
            return 'below_minimum';
        }

        return 'available';
    }
}
