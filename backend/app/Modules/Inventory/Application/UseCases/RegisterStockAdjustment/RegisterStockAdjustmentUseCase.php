<?php

namespace App\Modules\Inventory\Application\UseCases\RegisterStockAdjustment;

use App\Modules\Catalog\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\ProductId as CatalogProductId;
use App\Modules\Inventory\Application\UseCases\RegisterStockAdjustment\Dtos\RegisterStockAdjustmentInput;
use App\Modules\Inventory\Application\UseCases\RegisterStockAdjustment\Dtos\RegisterStockAdjustmentOutput;
use App\Modules\Inventory\Domain\Exceptions\InsufficientStockException;
use App\Modules\Inventory\Domain\Factories\InventoryItemFactory;
use App\Modules\Inventory\Domain\Factories\StockMovementFactory;
use App\Modules\Inventory\Domain\Repositories\InventoryItemRepository;
use App\Modules\Inventory\Domain\Repositories\StockMovementRepository;
use App\Modules\Inventory\Domain\ValueObjects\InventoryItemId;
use App\Modules\Inventory\Domain\ValueObjects\MovementQuantity;
use App\Modules\Inventory\Domain\ValueObjects\MovementReason;
use App\Modules\Inventory\Domain\ValueObjects\StockAdjustmentDirection;
use App\Modules\Inventory\Domain\ValueObjects\StockEntryType;
use App\Modules\Inventory\Domain\ValueObjects\StockMovementId;
use App\Modules\Inventory\Domain\ValueObjects\StockOutputType;
use App\Modules\Inventory\Domain\ValueObjects\StockProductId;
use App\Modules\Inventory\Domain\ValueObjects\StockQuantity;
use App\Modules\Shared\Application\Contracts\InputDto;
use App\Modules\Shared\Application\Contracts\OutputDto;
use App\Modules\Shared\Application\Contracts\TransactionManager;
use App\Modules\Shared\Application\Contracts\UseCase;
use App\Modules\Tenant\Domain\ValueObjects\TenantId;
use DateTimeImmutable;
use Illuminate\Support\Str;

/**
 * @implements UseCase<RegisterStockAdjustmentInput, RegisterStockAdjustmentOutput>
 */
final readonly class RegisterStockAdjustmentUseCase implements UseCase
{
    public function __construct(
        private ProductRepository $products,
        private InventoryItemRepository $inventoryItems,
        private StockMovementRepository $stockMovements,
        private InventoryItemFactory $inventoryItemFactory,
        private StockMovementFactory $stockMovementFactory,
        private TransactionManager $transactionManager,
    ) {}

    public function execute(InputDto $input): OutputDto
    {
        assert($input instanceof RegisterStockAdjustmentInput);

        return $this->transactionManager->run(function () use ($input): RegisterStockAdjustmentOutput {
            $tenantId = new TenantId($input->tenantId);
            $catalogProductId = new CatalogProductId($input->productId);
            $stockProductId = new StockProductId($input->productId);
            $direction = new StockAdjustmentDirection($input->direction);

            if ($this->products->findById($tenantId, $catalogProductId) === null) {
                throw new ProductNotFoundException;
            }

            $quantity = new MovementQuantity($input->quantity);
            $inventoryItem = $this->inventoryItems->findByProductId($tenantId, $stockProductId);
            $isNewInventoryItem = $inventoryItem === null;

            if ($direction->isEntry()) {
                if ($inventoryItem === null) {
                    $inventoryItem = $this->inventoryItemFactory->create(
                        id: new InventoryItemId((string) Str::uuid()),
                        tenantId: $tenantId,
                        productId: $stockProductId,
                        currentStock: new StockQuantity(0),
                    );
                }

                $inventoryItem->increase($quantity);
                $movement = $this->stockMovementFactory->createEntry(
                    id: new StockMovementId((string) Str::uuid()),
                    tenantId: $tenantId,
                    productId: $stockProductId,
                    userId: $input->userId,
                    type: new StockEntryType('manual_adjustment'),
                    quantity: $quantity,
                    reason: new MovementReason($input->reason),
                    note: $input->note,
                    unitCostInCents: null,
                    occurredAt: new DateTimeImmutable,
                );
            } else {
                if ($inventoryItem === null) {
                    throw new InsufficientStockException;
                }

                $inventoryItem->decrease($quantity);
                $movement = $this->stockMovementFactory->createOutput(
                    id: new StockMovementId((string) Str::uuid()),
                    tenantId: $tenantId,
                    productId: $stockProductId,
                    userId: $input->userId,
                    type: new StockOutputType('manual_adjustment'),
                    quantity: $quantity,
                    reason: new MovementReason($input->reason),
                    note: $input->note,
                    occurredAt: new DateTimeImmutable,
                );
            }

            if ($isNewInventoryItem) {
                $this->inventoryItems->save($inventoryItem);
            } else {
                $this->inventoryItems->update($inventoryItem);
            }

            $this->stockMovements->save($movement);

            return new RegisterStockAdjustmentOutput(
                movementId: $movement->id()->value,
                inventoryItemId: $inventoryItem->id()->value,
                tenantId: $tenantId->value,
                productId: $stockProductId->value,
                direction: $movement->direction(),
                type: $movement->type()->value,
                quantity: $movement->quantity()->value,
                currentStock: $inventoryItem->currentStock()->value,
                reason: $movement->reason()->value,
                note: $movement->note(),
                occurredAt: $movement->occurredAt()->format(DATE_ATOM),
            );
        });
    }
}
