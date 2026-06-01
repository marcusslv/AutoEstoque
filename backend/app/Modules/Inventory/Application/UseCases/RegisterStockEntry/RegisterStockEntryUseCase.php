<?php

namespace App\Modules\Inventory\Application\UseCases\RegisterStockEntry;

use App\Modules\Catalog\Domain\Exceptions\ProductNotFoundException;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Domain\ValueObjects\ProductId as CatalogProductId;
use App\Modules\Inventory\Application\UseCases\RegisterStockEntry\Dtos\RegisterStockEntryInput;
use App\Modules\Inventory\Application\UseCases\RegisterStockEntry\Dtos\RegisterStockEntryOutput;
use App\Modules\Inventory\Domain\Factories\InventoryItemFactory;
use App\Modules\Inventory\Domain\Factories\StockMovementFactory;
use App\Modules\Inventory\Domain\Repositories\InventoryItemRepository;
use App\Modules\Inventory\Domain\Repositories\StockMovementRepository;
use App\Modules\Inventory\Domain\ValueObjects\InventoryItemId;
use App\Modules\Inventory\Domain\ValueObjects\MovementQuantity;
use App\Modules\Inventory\Domain\ValueObjects\MovementReason;
use App\Modules\Inventory\Domain\ValueObjects\StockEntryType;
use App\Modules\Inventory\Domain\ValueObjects\StockMovementId;
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
 * @implements UseCase<RegisterStockEntryInput, RegisterStockEntryOutput>
 */
final readonly class RegisterStockEntryUseCase implements UseCase
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
        assert($input instanceof RegisterStockEntryInput);

        return $this->transactionManager->run(function () use ($input): RegisterStockEntryOutput {
            $tenantId = new TenantId($input->tenantId);
            $catalogProductId = new CatalogProductId($input->productId);
            $stockProductId = new StockProductId($input->productId);

            if ($this->products->findById($tenantId, $catalogProductId) === null) {
                throw new ProductNotFoundException;
            }

            $quantity = new MovementQuantity($input->quantity);

            $inventoryItem = $this->inventoryItems->findByProductId($tenantId, $stockProductId);
            $isNewInventoryItem = $inventoryItem === null;

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
                type: new StockEntryType($input->type),
                quantity: $quantity,
                reason: new MovementReason($input->reason),
                note: $input->note,
                unitCostInCents: $input->unitCostInCents,
                occurredAt: new DateTimeImmutable,
            );

            if ($isNewInventoryItem) {
                $this->inventoryItems->save($inventoryItem);
            } else {
                $this->inventoryItems->update($inventoryItem);
            }

            $this->stockMovements->save($movement);

            return new RegisterStockEntryOutput(
                movementId: $movement->id()->value,
                inventoryItemId: $inventoryItem->id()->value,
                tenantId: $tenantId->value,
                productId: $stockProductId->value,
                type: $movement->type()->value,
                quantity: $movement->quantity()->value,
                currentStock: $inventoryItem->currentStock()->value,
                reason: $movement->reason()->value,
                note: $movement->note(),
                unitCostInCents: $movement->unitCostInCents(),
                occurredAt: $movement->occurredAt()->format(DATE_ATOM),
            );
        });
    }
}
